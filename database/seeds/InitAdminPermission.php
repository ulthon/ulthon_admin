<?php

use think\migration\Seeder;
use app\model\AdminPermission;

class InitAdminPermission extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $permission_content = '
        [
            {
                "id" : 3,
                "name" : "系统设置",
                "app" : "admin",
                "controller" : "System",
                "action" : "index",
                "is_log" : 1
            },
            {
                "id" : 9,
                "name" : "系统第三方设置",
                "app" : "admin",
                "controller" : "System",
                "action" : "others",
                "is_log" : 1
            },
            {
                "id" : 12,
                "name" : "登录页面",
                "app" : "admin",
                "controller" : "Login",
                "action" : "index",
                "is_log" : 1
            },
            {
                "id" : 13,
                "name" : "登录验证",
                "app" : "admin",
                "controller" : "Login",
                "action" : "auth",
                "is_log" : 1
            },
            {
                "id" : 18,
                "name" : "退出",
                "app" : "admin",
                "controller" : "Login",
                "action" : "logout",
                "is_log" : 1
            },
            {
                "id" : 21,
                "name" : "系统设置更新",
                "app" : "admin",
                "controller" : "System",
                "action" : "update",
                "is_log" : 1
            },
            {
                "id" : 24,
                "name" : "管理员权限-删除",
                "app" : "admin",
                "controller" : "AdminPermission",
                "action" : "delete",
                "is_log" : 0
            },
            {
                "id" : 25,
                "name" : "管理员权限-列表",
                "app" : "admin",
                "controller" : "AdminPermission",
                "action" : "index",
                "is_log" : 0
            },
            {
                "id" : 26,
                "name" : "后台首页",
                "app" : "admin",
                "controller" : "Index",
                "action" : "index",
                "is_log" : 0
            },
            {
                "id" : 27,
                "name" : "管理员分组-列表",
                "app" : "admin",
                "controller" : "AdminGroup",
                "action" : "index",
                "is_log" : 0
            },
            {
                "id" : 29,
                "name" : "文件-列表",
                "app" : "admin",
                "controller" : "File",
                "action" : "index",
                "is_log" : 0
            },
            {
                "id" : 30,
                "name" : "管理员帐号-列表",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "index",
                "is_log" : 1
            },
            {
                "id" : 31,
                "name" : "管理员权限-保存编辑",
                "app" : "admin",
                "controller" : "AdminPermission",
                "action" : "update",
                "is_log" : 0
            },
            {
                "id" : 32,
                "name" : "管理员-编辑（登陆的人自己改自己）",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "edit",
                "is_log" : 0
            },
            {
                "id" : 33,
                "name" : "管理员日志-列表",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "adminLog",
                "is_log" : 0
            },
            {
                "id" : 34,
                "name" : "管理员-改密码（自己改自己）",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "password",
                "is_log" : 0
            },
            {
                "id" : 35,
                "name" : "管理员分组-添加",
                "app" : "admin",
                "controller" : "AdminGroup",
                "action" : "create",
                "is_log" : 0
            },
            {
                "id" : 36,
                "name" : "管理员分组-保存添加",
                "app" : "admin",
                "controller" : "AdminGroup",
                "action" : "save",
                "is_log" : 0
            },
            {
                "id" : 37,
                "name" : "管理员分组-删除",
                "app" : "admin",
                "controller" : "AdminGroup",
                "action" : "delete",
                "is_log" : 0
            },
            {
                "id" : 38,
                "name" : "管理员分组-编辑",
                "app" : "admin",
                "controller" : "AdminGroup",
                "action" : "edit",
                "is_log" : 0
            },
            {
                "id" : 39,
                "name" : "管理员分组-保存编辑",
                "app" : "admin",
                "controller" : "AdminGroup",
                "action" : "update",
                "is_log" : 0
            },
            {
                "id" : 40,
                "name" : "管理员-保存更新",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "update",
                "is_log" : 0
            },
            {
                "id" : 41,
                "name" : "文件-磁盘清空",
                "app" : "admin",
                "controller" : "File",
                "action" : "clear",
                "is_log" : 0
            },
            {
                "id" : 42,
                "name" : "管理员帐号-添加",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "create",
                "is_log" : 0
            },
            {
                "id" : 43,
                "name" : "管理员帐号-保存添加",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "save",
                "is_log" : 0
            },
            {
                "id" : 45,
                "name" : "管理员帐号-编辑",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "editAccount",
                "is_log" : 0
            },
            {
                "id" : 46,
                "name" : "管理员帐号-删除",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "delete",
                "is_log" : 0
            },
            {
                "id" : 47,
                "name" : "管理员帐号-保存编辑",
                "app" : "admin",
                "controller" : "Admin",
                "action" : "updateAccount",
                "is_log" : 0
            }
        ]      
        ';

        $permissions = json_decode($permission_content,true);

        foreach ($permissions as $permission) {
            $current_access_info = [
                'app'=>$permission['app'],
                'controller'=>$permission['controller'],
                'action'=>$permission['action'],
            ];
            $model_permission = AdminPermission::where($current_access_info)->find();

            if(empty($model_permission)){
                $current_access_info['name'] = $permission['name'];
                AdminPermission::create($current_access_info);
            }
        }
    }
}