## Project Introduction

For a better development experience.

Only for `developers' services, only for `demand customization' services.

A rapidly developed backend management system based on ThinkPHP6.1 and layui2.8.

Technical exchange QQ group: [207160418](https://jq.qq.com/?_wv=1027&k=TULvsosz)

## Installation tutorial

>ulthon_admin uses Composer to manage project dependencies. Therefore, before using ulthon_admin, make sure your machine has Composer installed.

> It is recommended to set the composer's image to the Alibaba image source

### Download the installation package through git, and install dependency packages through composer

```bash
The first step is to download the installation package
git clone https://gitee.com/ulthon/ulthon_admin

Or use composer to create
composer create-project ulthon/ulthon_admin


The second step is to install dependent packages (can be ignored when created using composer)
composer install

The third step, configure `.env`
Copy `.example.env` to `.env`
Modify `env` file

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


Step 4, install the database
php think migrate:run

Step 5: Initialize database data
php think seed:run

Finally, run locally temporarily
php think run

```

> This installation method is very friendly to the development experience

### ~~Download the complete package~~

The complete package download method is being updated.


### Online installation (initialization) database

The framework does not have the function of online installation, and it will not be provided built-in in the future.

However, ulthon_admin uses the database migration tool to install the database. It does not have to be used in the command line environment. It can also be used in ordinary controllers. We provide a simple code script to demonstrate how to install it online.

[How to install the database online](https://doc.ulthon.com/read/augushong/ulthon_admin/online_install.html)

## Why choose ulthon_admin

- No `plug-in ecology` and `application market`, no historical baggage and development baggage
- **Keep up to date with the latest technology stack and development ideas**
- **Continuously improve the development experience**
- You want to make a product or customized project that can be developed and maintained in the long term
- You need a project that can reach a certain level of functional construction with simple code development
- You want a **complete** tutorial document
   - ulthon_admin documentation will be continuously improved
   - The document agrees on detailed directory specifications, code specifications, code specification management tool configuration files, etc.
   - Documentation showing detailed best practices and feature use cases
   - Documentation includes frequently asked questions and low-level introduction
- Standard dependency management supports streamlining and customization based on actual conditions

## Site address

* Official website: [http://admin.demo.ulthon.com](http://admin.demo.ulthon.com)

* Document address: [http://doc.ulthon.com/home/read/ulthon_admin/home.html](http://doc.ulthon.com/home/read/ulthon_admin/home.html)

* Demo address: [http://admin.demo.ulthon.com/admin](http://admin.demo.ulthon.com/admin) (Account: admin, password: 123456. Note: Only permission to view information )
 
## Code repository

* Gitee address: [https://gitee.com/ulthon/ulthon_admin](https://gitee.com/ulthon/ulthon_admin)


## Project Features
* Compatible with PHP8.1
     * Minimum version PHP7.4
* Support mobile form to card conversion
* Support multiple skins
     * standard
     * quasi-object
     * Prototype
     * Sci-fi
     *GTK
     * pixels
     *WIN7
* Fast CURD command line
     * Generate controller, model, view and JS files with one click
     * Support related queries, field settings, etc.
     * Supports generating **database migration code**
     * Supports generating **property declarations for model fields**
* Permission management system based on `auth`
     * Implement `auth` authority node management through `annotation`
     * One-click update of `auth` permission nodes, no need to manually enter management
     * Complete back-end permission verification and front page button display and hide control
* Perfect menu management
     * Management by modules
     * Infinitus menu
     * Menu editing will prompt `Permission Node`
* Complete upload component function
     * Local storage
     *Alibaba Cloud OSS`recommended`
     * Tencent Cloud COS
     * Qiniuyun OSS
* Complete front-end component functions
    * Re-encapsulate layui's form form, eliminating the need to manually splice data requests
    * Simple and easy-to-use `picture and file` upload component
    * Simple and easy-to-use rich text editor `ckeditor`
    * Re-encapsulate the pop-up layer and use it in a minimalist way
    * Re-encapsulate the table to make it more comfortable to use
    * Encapsulate again according to the `cols` parameter of the table, provide interfaces to implement functions such as `image`, `switch`, `list`, etc., and then basically expand it yourself
    * Generate `search form` according to table parameters with one click, no need to write it yourself
* Use database for logging by default
* Deploy static resources to OSS with one click
    * All files in the `public\static` directory can be deployed with one click
    * One configuration item switches static resources (oss/local)
* Upload file record management
* Customize the background path to prevent others from finding the corresponding background address
* Highly customizable
   * Can streamline code functions
   * Support customized deletion of unnecessary dependencies


## new version update

Keep in sync with the versions of thinkPHP and layui.

In the future, whenever a new feature is implemented, a tag will be released.

> The main meaning of tag is to facilitate querying documents and comparing differences. (ulthon_admin itself is for customization and will not be forced to update)

## Open Source Agreement

Mulan Open Source Agreement

## What is

`tp6 background`, `thinkphp6 background`, `layui background`, `curd background`

## Skin Preview

> Supports a variety of special effect skins, please go to [Demo Site](http://admin.demo.ulthon.com) for more information

### standard
Well-behaved, concise and generous, steady yet lively.
![](/public/static/index/images/preview/normal.png)
### Skeuomorphism
Elegance is coming! Become an elegant programmer from now on.
![](/public/static/index/images/preview/neomorphic.png)
### Science Fiction
Suitable for night use and unconventional backend use such as Internet of Things systems, monitoring systems, and large-screen systems.
![](/public/static/index/images/preview/sifi.png)
### gnome
Feel the fear from gnome? A "Linux-compatible" backend framework.
![](/public/static/index/images/preview/gtk.png)

## Development dependencies

### Basic environment

Only the most basic PHP development environment is required.

- PHP8.0 (PATH environment variable is set correctly)
- composer
- Mysql5.7+ (necessary for development)

In the development environment, it is not necessary to install nginx, apache, ftp and other software, and you can develop directly through the built-in server.

> In fact, if you use SQLite to develop, you don't even want to install MySQL, but SQLite cannot adjust data tables and columns very well, so you generally use conventional databases such as MySQL.

### SASS

Some underlying components in the framework use SASS features, but generally you don’t need to worry about it. If you use vscode, you can refer to the following:

```
Name: Live Sass Compiler
ID: glenn2223.live-sass
Description: Compile Sass or Scss to CSS at realtime.
Version: 5.5.1
Posted by: Glenn Marks
VS Marketplace link: https://marketplace.visualstudio.com/items?itemName=glenn2223.live-sass
```

### Configuration

Configuration of liveSassCompiler in vscode:

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