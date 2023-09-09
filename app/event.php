<?php

// 事件定义文件

use app\common\event\AdminLoginSuccess\LogEvent;

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
    ],

    'subscribe' => [
    ],
];

$listen = include __DIR__ . '/listen.php';

$event['listen'] = array_merge($event['listen'], $listen);

return $event;
