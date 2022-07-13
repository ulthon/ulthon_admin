<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------

use app\common\command\admin\Version;
use app\common\command\admin\ResetPassword;
use app\common\command\Timer;

return [
    // 指令定义
    'commands' => [
        'curd'      => 'app\common\command\Curd',
        'node'      => 'app\common\command\Node',
        'OssStatic' => 'app\common\command\OssStatic',
        ResetPassword::class,
        Timer::class,
        Version::class
    ],
];
