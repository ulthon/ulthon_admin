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
use app\model\SystemConfig;
use think\facade\Cache;
use League\Flysystem\Util\MimeType;
use think\File;
use think\facade\Filesystem;
use app\model\UploadFiles;

function json_message($data = [], $code = 0, $msg = '')
{
  if (is_string($data)) {

    $code = $code === 0 ? 500 : $code;
    $msg = $data;
    $data = [];
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
    $list = SystemConfig::column('value', 'name');
  }

  if ($name === '') {
    return $list;
  }

  if (isset($list[$name])) {
    return $list[$name];
  }

  return $default;
}

function get_source_link($url)
{
  if (strpos($url, '/') === 0) {
    return $url;
  }
  if (strpos($url, 'http') === 0) {
    return $url;
  } else {
    $resource_domain = get_system_config('resource_domain');

    if (empty($resource_domain)) {
      $resource_domain = request()->host();
    }
    return 'http://' . $resource_domain . '/' . $url;
  }
}

function de_source_link($url)
{
  $domain = 'http://' . get_system_config('resource_domain') . '/';
  if (strpos($url, $domain) === 0) {
    return str_replace($domain, '', $url);
  }
  return false;
}

function save_url_file($url, $type)
{

  $file_data = geturl($url);

  $mime_type = MimeType::detectByContent($file_data);

  $ext_name = array_search($mime_type, MimeType::getExtensionToMimeTypeMap());
  $temp_file = tempnam(app()->getRuntimePath(), 'url_save_') . '.' . $ext_name;
  file_put_contents($temp_file, $file_data);
  $file = new File($temp_file);

  $save_name = Filesystem::putFile('wx_public_account/qrcode_url', $file, 'unique');

  $model_file = new UploadFiles();
  $model_file->file_name = $file->getFilename();
  $model_file->mime_type = $mime_type;
  $model_file->ext_name = $file->extension();
  $model_file->file_size = $file->getSize();
  $model_file->file_md5 = $file->md5();
  $model_file->file_sha1 = $file->sha1();
  $model_file->create_time = time();
  $model_file->type = $type;

  $model_file->save_name = $save_name;
  $model_file->save();

  return $save_name;
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
