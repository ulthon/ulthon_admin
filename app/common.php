<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use app\model\Admin;
use app\model\AdminPermission;
use app\model\SystemConfig;
use think\facade\Cache;
use think\facade\Session;
use think\route\Url as RouteUrl;

function json_message($data = [], $code = 0, $msg = '')
{
  if (is_string($data)) {

    if (strpos($data, 'http') === 0 || strpos($data, '/') === 0) {
      $data = [
        'jump_to_url' => $data
      ];
    } else {

      $code = $code === 0 ? 500 : $code;
      $msg = $data;
      $data = [];
    }
  } else if ($data instanceof RouteUrl) {
    $data = [
      'jump_to_url' => (string)$data
    ];
  }

  return json([
    'code' => $code,
    'msg' => $msg,
    'data' => $data
  ]);
}
function get_system_config($name = '', $default = '')
{
  $list = Cache::get('system_config');

  if (empty($list)) {
    try {

      $list = SystemConfig::column('value', 'name');
    } catch (\Throwable $th) {
      return $default;
    }
  }

  if ($name === '') {
    return $list;
  }

  if (isset($list[$name])) {
    return $list[$name];
  }

  return $default;
}

function get_source_link($url,$default = '')
{
  if (empty($url)) {

    if(!empty($default)){
      $url = $default;
    }else{
      $url = '/static/images/avatar.png';
    }
  }
  if (strpos($url, '/') === 0) {
    return request()->domain() . $url;
  }
  if (strpos($url, 'http') === 0) {
    return $url;
  } else {
    $resource_domain = get_system_config('resource_domain');

    if (empty($resource_domain)) {
      $resource_domain = request()->domain();
    }
    return $resource_domain . '/' . $url;
  }
}

function de_source_link($url)
{
  $domain = get_system_config('resource_domain') . '/';
  if (strpos($url, $domain) === 0) {
    return str_replace($domain, '', $url);
  }
  return false;
}

function geturl($url)
{
  $headerArray = array();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
  $output = curl_exec($ch);
  curl_close($ch);

  return $output;
}


function posturl($url, $data)
{
  $data  = json_encode($data);
  $headerArray = array();
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($curl);
  curl_close($curl);
  return $output;
}

function format_size($filesize)
{

  if ($filesize >= 1073741824) {

    $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
  } elseif ($filesize >= 1048576) {

    $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
  } elseif ($filesize >= 1024) {

    $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
  } else {

    $filesize = $filesize . ' 字节';
  }

  return $filesize;
}



/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int   $pid
 * @param int   $level
 * @return array
 */
function array2level($array, $pid = 0, $level = 1)
{

  static $list = [];
  if ($level == 0) {
    $list = [];
    $level = 1;
  }
  foreach ($array as $v) {
    if ($v['pid'] == $pid) {
      $v['level'] = $level;
      $list[]     = $v;
      array2level($array, $v['id'], $level + 1);
    }
  }
  // halt($list);

  return $list;
}


function check_permission($key, $admin_id = null)
{
  if (is_null($admin_id)) {
    $admin_id = Session::get('admin_id');
  }

  if (empty($admin_id)) {
    return true;
  }

  if ($admin_id == 1) {
    return true;
  }

  $model_admin = Admin::cache(60)->find($admin_id);

  if (empty($model_admin->getData('group_id'))) {
    return true;
  }


  $cache_key = 'permission_' . $key;

  $model_permission = Cache::get($cache_key);
  if (empty($model_permission)) {
    $model_permission = AdminPermission::where('key', $key)->find();
    Cache::set($cache_key, $model_permission);
  }

  if (empty($model_permission)) {
    $model_permission = AdminPermission::create([
      'key' => $key
    ]);
    Cache::set($cache_key, $model_permission, 60);
  }

  if (in_array($model_permission->id, $model_admin->group->permissions)) {
    return true;
  }

  return false;
}


function get_order_sn($start = '', $end = '')
{
  return $start . date('YmdHis') . mt_rand(1000, 9999) . $end;
}

/**
 * 多应用下的url生成器
 * 在这里的@后面跟随的首先被认为成应用名而不是源文档的域名(或子域名)
 * 程序会尝试找到应用对应的域名来生成地址,如果没找到,则按照源文档的逻辑执行
 * @param string $url 
 * @param array $vars
 * @param boolean $suffix
 * @param boolean $domain
 * @return void
 */
function app_url(string $url = '', array $vars = [], $suffix = true, $domain = false): RouteUrl
{

  $url_result = explode('@', $url);
  // 在这里,@首先认为是应用名,而不是域名(或子域名)
  if (isset($url_result[1])) {
    $app_default_doamin = config('app.app_default_doamin');
    if (empty($app_default_doamin)) {
      $app_domain_bind = config('app.domain_bind');

      if (!empty($app_domain_bind)) {
        $app_default_doamin = array_flip($app_domain_bind);
      }
    }

    if (isset($app_default_doamin[$url_result[1]]) && $app_default_doamin[$url_result[1]] != '*') {
      $url = $url_result[0] . "@" . $app_default_doamin[$url_result[1]];
    }
  }

  return url($url, $vars, $suffix, $domain);
}
