<?php

// 事件定义文件

use app\common\event\AdminLoginSuccess\LogEvent;
use app\common\event\AdminLoginType\DemoEvent;

$event = [
    'bind' => [
    ],

    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'AdminLoginSuccess' => [
            LogEvent::class,
        ],
        'AdminLoginType' => [
            DemoEvent::class,
        ],
    ],

    'subscribe' => [
    ],
];

$listen = include __DIR__ . '/common/app/listen.php';

$event['listen'] = array_merge($event['listen'], $listen);

return $event;
