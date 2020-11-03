<?php

use think\migration\Seeder;
use app\model\Admin;
use think\helper\Str;

class InitAdmin extends Seeder
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
        $account['account'] = 'admin';
        $account['salt'] = Str::random(6);
        $account['password'] = md5('123456'.$account['salt']);
        $account['avatar'] = '/static/images/avatar.png';

        $model_admin = Admin::where('account',$account['account'])->find();

        if(empty($model_admin)){
            $model_admin = new Admin;
            $model_admin->data($account);
            $model_admin->save();
        }
    }
}