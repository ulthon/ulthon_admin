<?php

use think\migration\Seeder;
use app\model\SystemConfig;
use think\facade\Cache;

class InitSystemConfig extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {

        $data = [
            'site_name'=>'奥宏后台管理模板'
        ];

        $list = get_system_config();

        foreach ($data as $key => $value) {

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
    }
}