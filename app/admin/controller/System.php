<?php

namespace app\admin\controller;

use think\Request;
use think\facade\View;
use app\model\SystemConfig;
use think\facade\Cache;
use app\model\UploadFiles;
use EasyWeChat\Factory;
use think\facade\Config;
use app\model\WxPublicAccount;

class System extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch();
    }

    public function others()
    {

        return View::fetch();
    }

    public function update()
    {

        $upload_files_config = [
            'site_logo'
        ];

        $post_data = $this->request->post();
        
        $list = SystemConfig::column('value','name');
        
        foreach ($post_data as $key => $value) {
            if(\in_array($key,$upload_files_config)){
                $old_save_name = get_system_config($key);
                UploadFiles::update(['used_time'=>time()],['save_name'=>$value]);
                if($old_save_name != $value){
                    UploadFiles::destroy(['save_name'=>$old_save_name]);
                }
            }
            if(isset($list[$key])){
                SystemConfig::where('name',$key)->update(['value'=>$value]);
            }else{
                $model_sysconfig = new SystemConfig();
                $model_sysconfig->name = $key;
                $model_sysconfig->value = $value;
                $model_sysconfig->save();
            }

            $list[$key] = $value;
        }
        
        Cache::set('system_config',$list);

        return $this->success();
    }
}
