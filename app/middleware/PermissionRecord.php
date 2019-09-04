<?php

namespace app\middleware;

use app\model\AdminPermission;
use app\Request;

class PermissionRecord
{
    public function handle(Request $request, \Closure $next)
    {

        $current_access_info = [
            'app'=>$request->app(),
            'controller'=>$request->controller(),
            'action'=>$request->action()
        ];

        $model_permission = AdminPermission::where($current_access_info)->find();

        if(empty($model_permission)){
            AdminPermission::create($current_access_info);
        }

        return $next($request);
    }
}
