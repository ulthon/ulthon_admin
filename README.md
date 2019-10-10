
# ulthon_admin

## 奥宏后台管理小模板


### 使用ThinkPHP6快速开始一个有管理后台的项目

#### 介绍

这不是一个完整的后台解决方案或产品,没有过多的功能和开发规则.

基于thinkphp6的系统后台管理模板,仅实现通用的基本的功能,基于ThinkPHP6,Layui,Jquery,支持各类数据库.


本项目的定位是实现几个基本的功能,节约您的一些开发时间,没有过多的开发限制.

比如每个后台都要有账号的登录/编辑,这种小的功能,几乎每次做项目时都要做,花时间又没有什么成就感,您可以使用本模板,节省这部分时间.

类似的功能还有服务器信息/系统配置等.

实现功能的同时没有制定更多的开发规则,您完全可以把本项目的代码修按照您的意愿改掉.

#### 最新演示

[在线演示](http://ulthon-admin.ulthon.com/admin)

账号: admin 密码: 123456


#### 快速试用


    1.安装
    git clone https://gitee.com/ulthon/ulthon_admin.git
    或者
    composer create-project ulthon/ulthon_admin:dev-master
    2.进入目录
    cd ulthon_admin/
    3.安装依赖
    composer install
    4.初始化数据库
    php think migrate:run
    php think seed:run
    5.使用内置服务器
    php think run -p 8010
    6.访问前台
    127.0.0.1:8010/index.php/index
    7.访问后台
    127.0.0.1:8010/index.php/admin

后台帐号密码：admin/123456

如果希望去掉index.php，可以参考tp文档，在nginx或apache环境配置，内置服务器必须带index.php

#### 功能

- 服务器信息(0.2h,已完成)
- 系统配置(0.5h,已完成)
- 管理员管理(0.5h已完成)
- 账户管理(0.5h,已完成)
- 用户管理(0.5h已完成)
- 权限管理(1h已完成)
- 文件管理(2h已完成)
- 后台日志(1h已完成)


### 开发注意

#### 后台页面仅仅使用了`TP`的模板包含特性


#### 支持所有(`TP6`支持的)类型数据库

填写正确的数据库连接配置,

执行`php think migrate:run`安装数据库

执行`php think seed:run`初始化数据

#### 使用了配置全局中间件


在这个中间件里把数据库的配置信息设置到项目中.

中间件: `\app\\middleware\ConfigInit`

#### 文件上传

经过这个类上传的文件会保存到`public`下,

`TP`原本配置会保存到`public/storage`下,本项目修改了配置,直接保存到`public`下.

类:`\app\api\controller\Files::save()`

## 版权协议

`木兰协议`

## 开发维护

[临沂奥宏网络科技有限公司](http://ulthon.com)