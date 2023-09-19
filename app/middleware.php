<?php

// 全局中间件定义文件

$middleware_default = [
    // 全局请求缓存
    // \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
    100 => \think\middleware\SessionInit::class,
];

$middleware_common = include_once __DIR__ . '/common/app/middleware.php';

$middleware = array_merge($middleware_default, $middleware_common);

ksort($middleware);

return $middleware;
