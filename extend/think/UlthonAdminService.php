<?php

namespace think;

use app\common\command\Test;
use app\common\event\AdminLoginSuccess\LogEvent;
use app\common\event\AdminLoginType\DemoEvent;
use app\common\provider\ExceptionHandle;
use app\common\provider\Request;
use app\common\provider\View;
use think\app\Service as AppService;
use think\captcha\CaptchaService;
use think\facade\App;
use think\migration\Service as MigrateService;

class UlthonAdminService extends Service
{
    public function boot()
    {
        // 绑定系统事件
        $event_listen = [
            'AppInit' => [],
            'HttpRun' => [],
            'HttpEnd' => [],
            'LogLevel' => [],
            'LogWrite' => [],
            'AdminLoginSuccess' => [
                LogEvent::class,
            ],
            'AdminLoginType' => [
                DemoEvent::class,
            ],
        ];

        $this->app->event->listenEvents($event_listen);

        // 注册验证码服务
        $this->app->register(CaptchaService::class);

        // 注册多应用服务
        $this->app->register(AppService::class);

        // 注册数据库迁移服务
        $this->app->register(MigrateService::class);

        // 绑定命令行
        $this->commands([
            Test::class,
        ]);

        // 绑定标识容器
        $provider_default = [
            'think\Request' => Request::class,
            'think\exception\Handle' => ExceptionHandle::class,
            'think\View' => View::class,
        ];

        $provider_setting = include App::getRootPath() . '/app/provider.php';

        if (isset($provider_setting['think\App'])) {
            unset($provider_setting['think\App']);
        }

        $provider = array_merge($provider_default, $provider_setting);

        $this->app->bind($provider);

        // 导入系统中间件

        $middleware = [
            // 全局请求缓存
            // \think\middleware\CheckRequestCache::class,
            // 多语言加载
            // \think\middleware\LoadLangPack::class,
            // Session初始化
            100 => middleware\SessionInit::class,
        ];

        $this->app->middleware->import($middleware);
    }
}
