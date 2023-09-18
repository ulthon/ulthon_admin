<?php

// 容器Provider定义文件

use app\common\provider\ExceptionHandle;
use app\common\provider\Request;
use app\common\provider\View;

$provider_default = [
    'think\Request' => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
    'think\View' => View::class,
];

$provider_common = include_once __DIR__ . '/app/common/app/provider.php';

return array_merge($provider_default, $provider_common);
