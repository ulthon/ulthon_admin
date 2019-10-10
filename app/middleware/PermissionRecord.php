<?php

namespace app\middleware;

use app\model\AdminPermission;
use app\Request;

class PermissionRecord
{
    public function handle(Request $request, \Closure $next)
    {

        $current_access_info = [
            'app'=>app('http')->getName(),
            'controller'=>$request->controller(),
            'action'=>$request->action()
        ];

        if(in_array('',$current_access_info)){
            return $next($request);
        }

        $model_permission = AdminPermission::where($current_access_info)->find();

        if(empty($model_permission)){
            AdminPermission::create($current_access_info);
        }

        return $next($request);
    }
}
