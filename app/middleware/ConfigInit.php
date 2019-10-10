<?php

namespace app\middleware;

use think\facade\Db;
use think\facade\Config;
use think\helper\Arr;

class ConfigInit
{
    public function handle($request, \Closure $next)
    {

        //设置存储
        $filesystem_config = Config::get('filesystem');
        Arr::set($filesystem_config,'default','public');

        Config::set($filesystem_config, 'filesystem');
        
        // 社微信开放平台
        // $wx_open_app = [];
        // $wx_open_app = Arr::add($wx_open_app,'app_id',get_system_config('wx_open_app_id'));
        // $wx_open_app = Arr::add($wx_open_app,'secret',get_system_config('wx_open_app_secret'));
        // $wx_open_app = Arr::add($wx_open_app,'token',get_system_config('wx_open_app_token'));
        // $wx_open_app = Arr::add($wx_open_app,'aes_key',get_system_config('wx_open_app_aes_key'));

        // Config::set($wx_open_app,'wx_open_app');


        return $next($request);
    }
}
