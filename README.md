

## 项目介绍

为了更好的开发体验。

只为`开发人员`服务,只为`需求定制`服务。

基于ThinkPHP8和layui2.8的快速开发的后台管理系统。*自定义扩展架构，支持自动更新上游框架代码。*

技术交流QQ群：[207160418](https://jq.qq.com/?_wv=1027&k=TULvsosz) 

## 安装教程

>ulthon_admin 使用 Composer 来管理项目依赖。因此，在使用 ulthon_admin 之前，请确保你的机器已经安装了 Composer。

> 建议设置composer的镜像为阿里镜像源

### 通过git下载安装包，composer安装依赖包

```bash
第一步，下载安装包
git clone https://gitee.com/ulthon/ulthon_admin

或者使用composer创建
composer create-project ulthon/ulthon_admin


第二步，安装依赖包(使用composer创建可忽略)
composer install

第三步, 配置`.env`
复制`.example.env`为`.env`
修改`env`文件

[DATABASE]
TYPE=mysql
HOSTNAME=host.docker.internal
DATABASE=ulthon
USERNAME=root
PASSWORD=root
HOSTPORT=3306
CHARSET=utf8
DEBUG=true
PREFIX=ul_


第四步, 安装数据库
php think migrate:run

第五步，初始化数据库数据
php think seed:run

最后，本地临时运行
php think run

```

> 这个安装方式对开发体验非常友好

### ~~下载完整包~~

完整包下载方式更新中。


### 在线安装(初始化)数据库

框架并没有在线安装的功能，以后也不会内置提供。

但ulthon_admin使用数据库迁移工具安装数据库，不一定要在命令行环境使用，在普通的控制器中也可以使用。我们提供一个简单地代码脚本演示如何在线安装。

[如何在线上安装数据库](https://doc.ulthon.com/read/augushong/ulthon_admin/online_install.html)

### 更新

直接运行命令，即可。
```
php think admin:update
```

> 注意：必须使用git管理您的代码，在更新时尽量您自己的所有改动提交，然后您可以对比更新的文件。

上游框架经常会增加新特性和修复细节问题，使用该命令会很方便的同步代码。

## 为什么要选择ulthon_admin

- 扩展架构，自动更新
- 不搞`插件生态`和`应用市场`，没有历史包袱和开发包袱
- **保持最新技术栈和开发思想**
- **不断完善开发体验**
- 你想做一个可以**长远**开发维护的产品或定制项目
- 你需要一个只要简单的代码开发就能达到一定功能建设水平的项目
- 你希望有一个**完善**的教程文档
  - ulthon_admin文档将不断完善
  - 文档约定详细的目录规范、代码规范、代码规范管理工具配置文件等
  - 文档展示详细的最佳实践和特性用例用法
  - 文档包括常见问题和底层介绍
- 标准的依赖管理，支持根据实际情况精简和定制

## 站点地址

* 官方网站：[http://admin.demo.ulthon.com](http://admin.demo.ulthon.com)

* 文档地址：[http://doc.ulthon.com/home/read/ulthon_admin/home.html](http://doc.ulthon.com/home/read/ulthon_admin/home.html)

* 演示地址：[http://admin.demo.ulthon.com/admin](http://admin.demo.ulthon.com/admin)（账号：admin，密码：123456。备注：只有查看信息的权限）
 
## 代码仓库

* Gitee地址：[https://gitee.com/ulthon/ulthon_admin](https://gitee.com/ulthon/ulthon_admin)


## 项目特性
* 兼容PHP8.1
    * 最低版本PHP7.4
* 支持移动端表格转卡片
* 支持多款皮肤
    * 标准
    * 拟物
    * 原型
    * 科幻
    * GTK
    * 像素
    * WIN7
* 快速CURD命令行
    * 一键生成控制器、模型、视图、JS文件
    * 支持关联查询、字段设置等等
    * 支持生成**数据库迁移代码**
    * 支持生成**模型字段的属性声明**
* 基于`auth`的权限管理系统
    * 通过`注解方式`来实现`auth`权限节点管理
    * 具备一键更新`auth`权限节点，无需手动输入管理
    * 完善的后端权限验证以及前面页面按钮显示、隐藏控制
* 完善的菜单管理
    * 分模块管理
    * 无限极菜单
    * 菜单编辑会提示`权限节点`
* 完善的上传组件功能
    * 本地存储
    * 阿里云OSS`建议使用`
    * 腾讯云COS
    * 七牛云OSS
* 完善的前端组件功能
   * 对layui的form表单重新封装，无需手动拼接数据请求
   * 简单好用的`图片、文件`上传组件
   * 简单好用的富文本编辑器`ckeditor`
   * 对弹出层进行再次封装，以极简的方式使用
   * 对table表格再次封装，在使用上更加舒服
   * 根据table的`cols`参数再次进行封装，提供接口实现`image`、`switch`、`list`等功能，再次基础上可以自己再次扩展
   * 根据table参数一键生成`搜索表单`，无需自己编写
* 默认使用数据库记录日志
* 一键部署静态资源到OSS上
   * 所有在`public\static`目录下的文件都可以一键部署
   * 一个配置项切换静态资源（oss/本地）
* 上传文件记录管理
* 后台路径自定义，防止别人找到对应的后台地址
* 高度可定制性
  * 可以精简代码功能
  * 支持定制删除不需要的依赖


## 版本更新

保持和thinkPHP、layui的版本同步。

以后每当实现一个新特性则发布一个tag。

> tag的主要意义是更新底层代码。如果您只改动了app和config下的文件，那么一般情况下，可以通过命令随便更新底层框架。

## 开源协议

木兰开源协议

## 是什么

`tp8后台`，`thinkphp8后台`，`layui后台`,`curd后台`

## 皮肤预览

> 支持多款特效皮肤，更多请前往[演示站点](http://admin.demo.ulthon.com) 查看

### 标准
规规矩矩，简洁大方，稳重不失活泼。
![](/public/static/index/images/preview/normal.png)
### 拟物
优雅来袭！从现在开始做一个优雅的程序员。
![](/public/static/index/images/preview/neomorphic.png)
### 科幻
适合夜间使用，适合物联网系统、监控系统、大屏系统等非常规后台使用。
![](/public/static/index/images/preview/sifi.png)
### gnome
感受到来自gnome的恐惧了吗？一个“兼容Linux”的后台框架。
![](/public/static/index/images/preview/gtk.png)

## 开发依赖

### 基本环境

只需要最基础的PHP开发环境即可。

- PHP8.0（正确设置PATH环境变量）
- composer
- Mysql5.7+（开发必备）

开发环境中，并不必须安装nginx、apache、ftp等软件，可以直接通过内置服务器进行开发。

> 实际上，如果你使用sqlite开发，连mysql都不想要安装，但是sqlite并不能很好地调整数据表和列，所以一般使用mysql等常规数据库。

### SASS

框架中部分底层组件使用了SASS特性，但一般不需要关心，如果使用vscode，可参考以下内容：

```
名称: Live Sass Compiler
ID: glenn2223.live-sass
说明: Compile Sass or Scss to CSS at realtime.
版本: 5.5.1
发布者: Glenn Marks
VS Marketplace 链接: https://marketplace.visualstudio.com/items?itemName=glenn2223.live-sass
```

### 配置

vscode中liveSassCompiler的配置:

```json
{
    "liveSassCompile.settings.includeItems": [
        "/public/static/common/css/theme/*.scss",
        "/public/static/plugs/lay-module/tableData/tableData.scss",
        "/public/static/plugs/lay-module/tagInput/tagInput.scss",
        "/public/static/plugs/lay-module/propertyInput/propertyInput.scss"
    ]
}
```
