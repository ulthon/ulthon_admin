<?php

// 升级的注意事项
return [
    [
        'version' => 'v2.0.57',
        'desc' => [
            '删除了localheinz/diff依赖，可以通过以下命令调整',
            'composer remove localheinz/diff',
            '',
            '调整了AdminUpdateServiceBase的代码的文件名和类名，可能需要手动修改或删除旧的文件',
        ],
    ],
    [
        'version' => 'v2.0.65',
        'desc' => [
            '商品表增加了自定义字段的属性输入字段，用于系统商品代码演示',
        ],
    ],
    [
        'version' => 'v2.0.71',
        'desc' => [
            '本次更新修改了database/migrations/20220419030557_system_auth.php文件，修复了安装到sqlite的问题，如果你使用sqlite，需要有意识的解决这个问题',
        ],
    ],
    [
        'version' => 'v2.0.74',
        'desc' => [
            '本次更新修改了js中init的各项属性的大小写规范，你需要将蛇形命名全局替换为小驼峰命名，比如:table_elem改为tableElem',
            '可以运行 php think admin:update:code --update-version v2.0.74 命令自动替换',
        ],
    ],
];
