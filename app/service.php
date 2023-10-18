<?php

declare(strict_types=1);

use think\UlthonAdminService;

$service = [
    // !注意，必须要注册该服务
    10 => UlthonAdminService::class,
];

return $service;
