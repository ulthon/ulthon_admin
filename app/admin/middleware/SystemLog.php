<?php

namespace app\admin\middleware;

use think\facade\Log;
use think\facade\Request as FacadeRequest;
use think\Request;

/**
 * 系统操作日志中间件
 * Class SystemLog.
 */
class SystemLog
{
    /**
     * 敏感信息字段，日志记录时需要加密.
     * @var array
     */
    protected $sensitiveParams = [
        'password',
        'password_again',
        'phone',
        'mobile',
    ];

    public function handle(Request $request, \Closure $next)
    {
        $params = $request->param();
        if (isset($params['s'])) {
            unset($params['s']);
        }
        foreach ($params as $key => $val) {
            in_array($key, $this->sensitiveParams) && $params[$key] = '***********';
        }
        $method = strtolower($request->method());
        $url = $request->url();

        if ($request->isAjax()) {
            if (in_array($method, ['post', 'put', 'delete'])) {
                $ip = FacadeRequest::ip();
                $data = [
                    'admin_id' => session('admin.id'),
                    'url' => $url,
                    'method' => $method,
                    'ip' => $ip,
                    'content' => json_encode($params, JSON_UNESCAPED_UNICODE),
                    'useragent' => $_SERVER['HTTP_USER_AGENT'],
                    'create_time' => time(),
                ];
                Log::debug(print_r($data, true));
            }
        }

        return $next($request);
    }
}
