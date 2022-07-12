<?php

use think\facade\Env;
use think\facade\Request;

return [
    // 默认磁盘
    'default' => Env::get('filesystem.driver', 'local'),
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'local_public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => Request::domain() . '/storage',
            // 可见性
            'visibility' => 'public',
        ],
        'qnoss' => [
            'type' => 'Qiniu'
        ],
        'alioss' => [
            'type' => 'Alioss'
        ],
        'txcos' => [
            'type' => 'Txcos'
        ],
        // 更多的磁盘配置信息
    ],
];
