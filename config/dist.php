<?php

return [
    // 全局函数库，否则无论是否以/开头，都以项目根目录开头定位，如果有其他的文件，在这里声明
    // 不支持项目以外的位置定义
    // 只能包含函数，不支持命名空间
    // 这些代码不能包含__DIR__、__FILE__等代码位置常量，不会影响打包，但代码逻辑会被破坏
    'function_path' => [
        'app/common.php'
    ],
    // 支持正则表达式，直接跳过所有文件
    'skip_path' => [
        '/^\.git/',
        '/^dist/',
        '/^runtime/',
    ],
    // 支持正则表达式，将文件原封不动的挪到输出目录
    'ignore_path' => [
        '/^vendor/',
        '/^config/',
        '/^lib\//',
        '/^database\/*/',
        '/event\.php/',
        '/middleware\.php/',
        '/provider\.php/',
        '/service\.php/',
        '/^app\/.*\/config\/.*/',
        '/app\/common.php/',
        '/config.php/',
        '/^public\/index\.php/',
        '/^public\/router\.php/',
        '/^route\/*/',
        '/^app\/admin\/service\/initAdminData\/*/',
    ]
];
