<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------

use app\command\make\View;

return [
    // 指令定义
    'commands' => [
        'app\command\ResetPassword',
        View::class
    ],
];
