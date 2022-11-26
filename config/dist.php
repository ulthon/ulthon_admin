<?php

return [

    // 支持正则表达式，直接跳过所有文件
    'skip_path' => [
        '/^\.git/',
        '/^\..+\//',
        '/^dist/',
        '/^build/',
        '/^runtime/',
    ],

    // 基础的加载规则
    'include_path' => [
        '/^app/',
        '/^extend/',
        '/^database/',
        '/^config/',
        '/^route/',
    ],

    'pack_app' => [
        // 应当是标准的thinkphp类库文件，比如控制器、模型等
        'include_path' => [
            "/^app/",
            "/^extend/",
        ],
        // 应当是thinkphp的其他非类库文件，比如配置、中间件配置、自定义加载类、函数库、路由等
        'exclude_path' => [],
    ],
    
    'pack_env' => [
        // 0:base64方式处理,1:明文打包,3:不要编译env配置
        'pack_env_mode' => 0
    ],
    'pack_vars' => [
        // 0:全部编译，1：关闭编译，2：不编译controller的方法
        'pack_vars_mode' => 0,
        'controller_path' => [
            '/\/controller\//',
        ]
    ],
    // 全局函数库，否则无论是否以/开头，都以项目根目录开头定位，如果有其他的文件，在这里声明
    // 不支持项目以外的位置定义
    // 只能包含函数，不支持命名空间
    // 这些代码将忽略include、require表达式，所以如果用include 方式引入其他函数库，要在这里声明，
    // !如果使用include方式读取文件内容或配置，将失败
    // !将忽略注释和注解，代码业务请不要依赖注解
    'function_path' => [
        'app/common.php'
    ],

    'copyright' => [
        'ulthon_admin后台管理系统框架',
        '版权所有 @2022 临沂奥宏网络科技有限公司，并保留所有权利',
        '官网地址: http://ulthon.com',
        '后台官网地址: http://admin.demo.ulthon.com',
        '这不是一个自由软件！也不是一个源码文件！',
        '您正在浏览这段文字说明您正在试图获取或修改代码',
        '不允许对程序代码以任何形式任何目的的再发布',
        '任何对当前文件的浏览、修改、反编译都是非法的，没有获取授权的',
    ]



];
