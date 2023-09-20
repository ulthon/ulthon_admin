<?php

declare(strict_types=1);

use think\migration\Service;

$service_default = [
    0 => 'think\\captcha\\CaptchaService',
    1 => 'think\\app\\Service',
    2 => Service::class,
];

$service_common = include_once __DIR__ . '/common/app/service.php';

$service = array_merge($service_default, $service_common);

ksort($service);

return $service;
