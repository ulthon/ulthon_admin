

新版基于`EasyAdmin`打造,`EasyAdmin`是一款优秀的开源后台框架,致谢.

## 项目介绍

只为`开发人员`服务,只为`需求定制`服务.

基于ThinkPHP6.0和layui的快速开发的后台管理系统。

技术交流QQ群：[207160418](https://jq.qq.com/?_wv=1027&k=TULvsosz) 

## 安装教程

>ulthon_admin 使用 Composer 来管理项目依赖。因此，在使用 ulthon_admin 之前，请确保你的机器已经安装了 Composer。

> 建议设置composer的镜像为阿里镜像源

#### 通过git下载安装包，composer安装依赖包

```bash
第一步，下载安装包
git clone https://gitee.com/ulthon/ulthon_admin


第二步，安装依赖包
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
    * 原型
    * 科幻
    * GTK
    * 像素
    * WIN7
* 快速CURD命令行
    * 一键生成控制器、模型、视图、JS文件
    * 支持关联查询、字段设置等等
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


## 版本更新

保持和thinkPHP、layui的版本同步。

以后每当实现一个新特性则发布一个tag。

> tag的主要意义是方便查询文档，比较差异。（ulthon_admin本身是为了定制，不会强制更新）

## 开源协议

木兰开源协议

## 与`EasyAdmin`的关系

`EasyAdmin`是一个优秀和流行的开源后台项目,新版的`ulthon_admin`相当于它的一个分支.

`ulthon_admin`将不会向`市场插件`方向发展,只为`开发人员`服务,只为`需求定制`服务.

自然而然的,无论是`ulthon_admin`还是`EasyAdmin`,都会不断优化自己的系统特性,在生态和文档上是互补的.


