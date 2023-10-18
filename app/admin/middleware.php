<?php

// !当前文件的内容应当与 /extend/base/admin/middleware.php 的内容一致，然后再根据实际情况设置

// 全局中间件定义文件
return [

    // Session初始化
    \think\middleware\SessionInit::class,

    // 系统操作日志
    \app\admin\middleware\SystemLog::class,

    // Csrf安全校验
    \app\admin\middleware\CsrfMiddleware::class,

];
