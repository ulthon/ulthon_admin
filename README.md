
# ulthon_admin

## 奥宏TP6后台管理模板


### 使用ThinkPHP6快速开始一个有管理后台的项目

#### 介绍

基于thinkphp6的系统后台管理模板.

基于ThinkPHP6,Layui,Jquery,支持各类数据库.

已实现很好用的`上传文件管理`,`内容管理`,`导航轮播管理(支持小程序)`的后台功能.

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

[本地测试后台链接](/index.php/admin)

如果希望去掉index.php，可以参考tp文档，在nginx或apache环境配置，内置服务器必须带index.php


#### 重置密码

重置密码为123456.

```
php think reset_password
```

#### 功能

- 服务器信息
- 系统配置
- 管理员管理
- 账户管理
- 用户管理
- 权限管理
- 文件管理
- 后台日志
- 支持轮播图,导航,小程序导航(打开方式)等设置
- 实现CMS后台
- 支持前台多主题
- 适配手机端,实现table转卡片样式

#### 多主题用法

在index应用下,使用`$this->fetch($template,$vars)`,而不要用`View::fetch($template,$vars)`.

多主题用法仅当`$template`为空字符串或者字符串有效,使用`跨应用`,`跨控制器`,`木板路径`,`数据内容`的写法无效.

index应用下控制器有个`BaseController.php`,里面有个属性`$isUseTpls = true`,当关闭时不使用多主题.

多主题规则,系统设置的`index_tpl_name`拼接`$template`;

例如下:
假设是`Index`控制器,`index`方法,系统设置的`index_tpl_name`值为`easy_blue_`,模板命名规则和模板目录均为默认情况下.

```
  $this->fetch();   
  // 定位模板为:/view/index/index/easy_blue_index.html

  $this->fetch('index');   
   // 定位模板为:/view/index/index/easy_blue_index.html

  $this->fetch('index_1');    
  // 定位模板为:/view/index/index/easy_blue_index_1.html

  $this->fetch('index_product');    
  // 定位模板为:/view/index/index/easy_blue_index_product.html

```

> 配置文件下的模板命名规则和模板位置仍然按有效,默认在`/view`将方法名转下划线的写法.这些配置仍然有效;

### 完整安装

先执行快速使用的步骤,此时项目已经安装到本地,数据库也安装到本地的sqlite.

部署到服务器,要做的只是把代码上传,连接正式数据库,并安装即可.

- 上传代码
    - 把代码上传到服务器上,很简单的事情,使用ftp,sftp都可以
    - 也可以在服务器上执行`快速试用`的几部,然后安装到正式数据库
- 安装数据库
    - 修改配置文件`config/database.php`,连接到正确的数据库
        - 一般是第七行的`sqlite`改成`mysql`
        - 给23行的mysql数组的配置改成正确的配置
    - 重新执行`初始化数据库`的两行命令
        - 执行之前可能需要清理下缓存`php think clear --cache`

> 理论上,数据库可以安装到sqlite,mysql,sqlserver,pgsql,如果出现问题,可能是`空和非空`,`默认值`等问题.欢迎大家测试反馈.


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

## 源码托管

[码云开源存储](https://gitee.com/ulthon/ulthon_admin)

## 开发维护

[临沂奥宏网络科技有限公司](http://ulthon.com)