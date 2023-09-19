<?php

declare(strict_types=1);

$service_default = [
    0 => 'think\\captcha\\CaptchaService',
    1 => 'think\\app\\Service',
];

$service_common = include_once __DIR__ . '/common/app/service.php';

$service = array_merge($service_default, $service_common);

ksort($service);

return $service;
