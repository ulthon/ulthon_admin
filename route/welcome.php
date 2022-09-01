<?php
use think\facade\Route;

Route::view('/', 'welcome', [
    'version' => time(),
    'data'    => [
        'description'        => '基于ThinkPHP6.0和Layui的快速开发的后台管理系统',
        'system_description' => '框架主要使用ThinkPHP6.0 + layui，拥有完善的权限的管理模块以及敏捷的开发方式，让你开发起来更加的舒服。项目以及文档还在持续完善，请保持关注。',
    ],
    'navbar'  => [
        [
            'name'   => '首页',
            'active' => true,
            'href'   => 'http://admin.demo.ulthon.com',
            'target' => '_self',
        ],
        [
            'name'   => '文档',
            'active' => false,
            'href'   => 'http://doc.ulthon.com/home/read/ulthon_admin/home.html',
            'target' => '_blank',
        ],
        [
            'name'   => '演示',
            'active' => false,
            'href'   => 'http://admin.demo.ulthon.com/admin/',
            'target' => '_blank',
        ],
        [
            'name'   => '商业支持',
            'active' => false,
            'href'   => 'https://ulthon.com/bussiness.html',
            'target' => '_blank',
        ],
        [
            'name'   => 'QQ群',
            'active' => false,
            'href'   => 'https://jq.qq.com/?_wv=1027&k=TULvsosz',
            'target' => '_blank',
        ],
    ],
    'feature' => [
        [
            'name'        => '内置权限管理',
            'description' => '内置基于auth的权限系统，使用注解方式自动更新权限节点，无需手动维护。',
        ],
        [
            'name'        => '表格&表单的二次封装',
            'description' => '对layui的数据表格和表单进行二次封装，开发起来更舒服流畅。',
        ],
        [
            'name'        => '上传&附件管理',
            'description' => '内置封装上传方法以及上传的附件管理，支持上传到本地以及OSS，可以在此基础上自己去扩展。',
        ],
        [
            'name'        => '快速生成CURD模块',
            'description' => '完善的命令行开发模式, 一键生成控制器、模型、视图、JS等文件, 使开发速度快速提升。',
        ],
        [
            'name'        => '详细的开发规范',
            'description' => '通过文档详细约定各个文件和目录的用法',
        ],
        [
            'name'        => '丰富的表单组件',
            'description' => '搜索下拉框、多选组件、搜索输入组件、标签组件、表格选择组件、日期选择组件、城市选择组件等等',
        ],

    ],
]);
