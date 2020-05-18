<?php

use think\migration\Seeder;
use app\model\AdminPermission;

class InitAdminPermission extends Seeder
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
        $permission_content = '{}';

        $permissions = json_decode($permission_content,true);

        foreach ($permissions as $permission) {
            $current_access_info = [
                'app'=>$permission['app'],
                'controller'=>$permission['controller'],
                'action'=>$permission['action'],
            ];
            $model_permission = AdminPermission::where($current_access_info)->find();

            if(empty($model_permission)){
                $current_access_info['name'] = $permission['name'];
                AdminPermission::create($current_access_info);
            }
        }
    }
}