<?php

return [
    // 全局函数库，否则无论是否以/开头，都以项目根目录开头定位，如果有其他的文件，在这里声明
    // 不支持项目以外的位置定义
    // 只能包含函数，不支持命名空间
    // 这些代码将忽略include、require表达式，所以如果用include 方式引入其他函数库，要在这里声明，
    // !如果使用include方式读取文件内容或配置，将失败
    // !将忽略注释和注解，代码业务请不要依赖注解
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
        '/event\.php/',
        '/middleware\.php/',
        '/provider\.php/',
        '/service\.php/',
        '/^app\/.*\/config\/.*/',
        '/config.php/',
        '/^public\/index\.php/',
        '/^public\/router\.php/',

        '/^app\/admin\/service\/initAdminData\/*/',
    ],
    // 希望将env打包的文件
    'pack_env_path' => [
        '/^app/',
        '/^config/',
        '/^database/',
        '/^extend/',
        '/^lib/',
        '/^route/',
    ],
    // 0:base64方式处理,1:明文打包
    'pack_env_mode' => 1
];
