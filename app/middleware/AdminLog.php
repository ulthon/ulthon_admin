<?php

namespace app\middleware;

use app\model\AdminLog as AppAdminLog;
use app\model\AdminPermission;
use app\Request;
use think\Collection;
use think\facade\Cache;

class AdminLog
{
    public function handle(Request $request, \Closure $next)
    {
        $logged_admin_permission = Cache::get('logged_admin_permission');

        if(empty($logged_admin_permission)){
            $logged_admin_permission = new Collection(AdminPermission::where('is_log',1)->select());
        }


        $is_exit = $logged_admin_permission->where('app',app('http')->getName())
        ->where('controller',$request->controller())
        ->where('action',$request->action());

        if(!$is_exit->isEmpty()){
            AppAdminLog::create([
                'app'=>app('http')->getName(),
                'controller'=>$request->controller(),
                'action'=>$request->action(),
                'param'=>$request->param(),
                'create_time'=>time(),
                'admin_id'=>$request->session('admin_id','0'),
                'ip'=>$request->ip()
            ]);
        }

        return $next($request);

    }
}
