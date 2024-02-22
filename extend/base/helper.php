<?php

// 应用公共文件

use app\common\exception\EventException;
use app\common\service\AuthService;
use app\common\tools\StoreValueTools;
use think\exception\HttpResponseException;
use think\facade\App;
use think\facade\Cache;
use think\facade\Env;
use think\facade\Event;
use think\facade\Filesystem;
use think\response\View;
use think\route\Url;

if (!function_exists('__url')) {
    /**
     * 构建URL地址
     * @param string $url
     * @param array $vars
     * @param bool $suffix
     * @param bool $domain
     * @return string
     */
    function __url(string $url = '', array $vars = [], $suffix = true, $domain = false)
    {
        $url = url($url, $vars, $suffix, $domain)->build();

        $url_data = parse_url($url);

        $url_path = $url_data['path'];

        $url_arr = explode('/', $url_path);

        $app_map = config('app.app_map');

        $app_name = array_search($url_arr[1], $app_map);

        if (!empty($app_name)) {
            $url_arr[1] = $app_name;
        }

        $url_path = implode('/', $url_arr);

        $url_data['path'] = $url_path;

        $url = unparse_url($url_data);

        return $url;
    }
}

if (!function_exists('password')) {
    /**
     * 密码加密算法.
     * @param $value 需要加密的值
     * @param $type  加密类型，默认为md5 （md5, hash）
     * @return mixed
     */
    function password($value, $salt = '_encrypt')
    {
        $value = sha1('ul_' . $salt) . md5($value) . md5($salt) . sha1($value);

        return sha1($value);
    }
}

if (!function_exists('xdebug')) {
    /**
     * debug调试.
     * @deprecated 不建议使用，建议直接使用框架自带的log组件
     * @param string|array $data 打印信息
     * @param string $type 类型
     * @param string $suffix 文件后缀名
     * @param bool $force
     * @param null $file
     */
    function xdebug($data, $type = 'xdebug', $suffix = null, $force = false, $file = null)
    {
        !is_dir(runtime_path() . 'xdebug/') && mkdir(runtime_path() . 'xdebug/');
        if (is_null($file)) {
            $file = is_null($suffix) ? runtime_path() . 'xdebug/' . date('Ymd') . '.txt' : runtime_path() . 'xdebug/' . date('Ymd') . "_{$suffix}" . '.txt';
        }
        file_put_contents($file, '[' . date('Y-m-d H:i:s') . '] ' . "========================= {$type} ===========================" . PHP_EOL, FILE_APPEND);

        $str = '';

        if (is_string($data)) {
            $str = $data;
        } else {
            if (is_array($data) || is_object($data)) {
                $str = print_r($data, true);
            } else {
                $str = var_export($data, true);
            }
        }

        $str . PHP_EOL;

        $force ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
    }
}

if (!function_exists('sysconfig')) {
    /**
     * 获取系统配置信息.
     * @param $group
     * @param null|bool|string $name
     * @return array|mixed
     */
    function sysconfig($group, $name = null, $default = null)
    {
        if ($name === true) {
            $value = Cache::get('sysconfig_' . $group);

            if (empty($value)) {
                $value = \app\admin\model\SystemConfig::where('name', $group)->value('value');
                Cache::tag('sysconfig')->set('sysconfig_' . $group, $value);
            }
            if (is_null($value)) {
                return $default;
            }

            return $value;
        }

        $where = ['group' => $group];
        $value = empty($name) ? Cache::get("sysconfig_{$group}") : Cache::get("sysconfig_{$group}_{$name}");
        if (empty($value)) {
            if (!empty($name)) {
                $where['name'] = $name;
                $value = \app\admin\model\SystemConfig::where($where)->value('value');
                Cache::tag('sysconfig')->set("sysconfig_{$group}_{$name}", $value, 3600);
            } else {
                $value = \app\admin\model\SystemConfig::where($where)->column('value', 'name');
                Cache::tag('sysconfig')->set("sysconfig_{$group}", $value, 3600);
            }
        }
        if (is_null($value)) {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('array_format_key')) {
    /**
     * 二位数组重新组合数据.
     * @param $array
     * @param $key
     * @return array
     */
    function array_format_key($array, $key)
    {
        $newArray = [];
        foreach ($array as $vo) {
            $newArray[$vo[$key]] = $vo;
        }

        return $newArray;
    }
}

if (!function_exists('auth')) {
    /**
     * auth权限验证
     * @param $node
     * @return bool
     * @throws think\db\exception\DataNotFoundException
     * @throws think\db\exception\DbException
     * @throws think\db\exception\ModelNotFoundException
     */
    function auth($node = null)
    {
        $authService = new AuthService(session('admin.id'));
        $check = $authService->checkNode($node);

        return $check;
    }
}

function json_message($data = [], $code = 0, $msg = '')
{
    if (is_string($data)) {
        if (strpos($data, 'http') === 0 || strpos($data, '/') === 0) {
            $data = [
                'jump_to_url' => $data,
            ];
        } else {
            $code = $code === 0 ? 500 : $code;
            $msg = $data;
            $data = [];
        }
    } elseif ($data instanceof Url) {
        $data = [
            'jump_to_url' => (string) $data,
        ];
    }

    return json([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ]);
}

if (!function_exists('unparse_url')) {
    function unparse_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}

function build_upload_url($url, $upload_type = null)
{
    if (is_null($upload_type)) {
        $upload_type = sysconfig('upload', 'upload_type', 'local_public');
    }

    return Filesystem::disk($upload_type)->url($url);
}

function event_handle_result($name, $key, $type = 'all', $params = []) : array
{
    $list_result = Event::trigger($name, $params);

    $result = [];

    foreach ($list_result as $key_event => $value_event) {
        if (!isset($value_event[$key])) {
            if (Env::get('adminsystem.strict_event')) {
                throw new EventException("Event view {$name} trigger a result without a {$key}");
            }
            continue;
        }
        if ($type == 'all') {
            $result[] = $value_event[$key];
        } elseif ($type == 'last') {
            $result = [];
            $result[] = $value_event[$key];
        } elseif ($type == 'first') {
            $result[] = $value_event[$key];
            break;
        }
    }

    return $result;
}

function event_view_content($name)
{
    $list_result = event_handle_result($name, 'view_content');

    $content = implode('', $list_result);

    return $content;
}

function event_view_replace($content, $name)
{
    $list_result = event_handle_result($name, 'view_replace');

    $content_event = implode('', $list_result);

    if (empty($content_event)) {
        return $content;
    }

    return $content_event;
}

function event_view_replace_js($name)
{
    $list_result = event_handle_result($name, 'view_replace_js');

    $content_event = implode('', $list_result);

    return "<script id='event-replace-js-{$name}' type='text/plain'>{$content_event}</script>";
}

function event_response($name, $params = [])
{
    $list_result = event_handle_result($name, 'response', 'last', $params);

    if (empty($list_result)) {
        return;
    }

    $response = $list_result[0];

    if (is_string($response)) {
        $response = View::create($response);
    }

    throw new HttpResponseException($response);
}

/**
 * 以扩展的架构定位app下的文件位置.
 *
 * @param string $file_path 文件路径，不要以/开头，不需要以app开头，会自动定位 app 或 extend/base。
 * @return string
 */
function app_file_path($file_path)
{
    $app_file_path = App::getRootPath() . 'app' . DIRECTORY_SEPARATOR . $file_path;
    if (!is_file($app_file_path)) {
        $app_file_path = App::getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . $file_path;
    }

    return $app_file_path;
}

function array_to_table($array_tree, $prefix_key = '')
{
    $table = [];

    foreach ($array_tree as $key => $value) {
        if (is_array($value)) {
            $table = array_merge($table, array_to_table($value, $key . '.'));
        } else {
            $table[] = [
                'key' => $prefix_key . $key,
                'value' => $value,
            ];
        }
    }

    return $table;
}

function format_bytes($size, $delimiter = '')
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }

    return round($size, 2) . $delimiter . $units[$i];
}

function get_store_value($key, $default = null)
{
    return StoreValueTools::get($key, $default);
}

function set_store_value($key, $value)
{
    return StoreValueTools::set($key, $value);
}
