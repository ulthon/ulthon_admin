<?php

namespace app\admin\controller;

use think\Request;
use think\facade\View;
use app\model\SystemConfig;
use think\facade\Cache;
use EasyWeChat\Factory;
use think\facade\Config;
use app\model\WxPublicAccount;
use app\UploadFiles as AppUploadFiles;

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
    public function agreement()
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

            if(!is_string($value)){
                $value = serialize($value);
            }

            if(\in_array($key,$upload_files_config)){
                $old_save_name = get_system_config($key);
                AppUploadFiles::use($value);
                if($old_save_name != $value){
                    AppUploadFiles::delete($old_save_name);
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

    public function clearCache()
    {
      Cache::clear();

      return $this->success('清楚成功');
    }
}
