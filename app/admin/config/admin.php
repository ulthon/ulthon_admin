<?php

// !当前文件的内容应当与 /extend/base/admin/config/admin.php的内容保持一直，然后再根据实际情况设置

use think\facade\Env;

return [

    // 不需要验证登录的控制器
    'no_login_controller' => [
        'login',
    ],

    // 不需要验证登录的节点
    'no_login_node'       => [
        'login/index',
        'login/out',
    ],

    // 不需要验证权限的控制器
    'no_auth_controller'  => [
        'ajax',
        'login',
        'index',
    ],

    // 不需要验证权限的节点
    'no_auth_node'        => [
        'login/index',
        'login/out',
    ],

    'default_auth_check' => Env::get('adminsystem.default_auth_check', false)
];
