<?php

// 容器Provider定义文件

use app\common\provider\ExceptionHandle;
use app\common\provider\Request;
use app\common\provider\View;

return [
    'think\Request' => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
    'think\View' => View::class,
];
