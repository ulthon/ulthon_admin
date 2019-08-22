<?php

namespace app\admin\controller;

use think\Request;
use think\facade\View;
use think\facade\Validate;
use think\validate\ValidateRule as Rule;
use app\model\Admin;
use think\facade\Session;

class Login extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return View::fetch();
    }


    public function auth()
    {
        $post_data = $this->request->post();

        $validate = Validate::rule('account',Rule::isRequire())
        ->rule('password',Rule::isRequire());

        if(!$validate->check($post_data)){
            return json_message($validate->getError());
        }

        $model_admin = Admin::where('account',$post_data['account'])->find();

        if(empty($model_admin)){
            return json_message('帐号不存在');
        }

        if($model_admin->getData('password') !== md5($post_data['password'].$model_admin->getData('salt'))){
            return json_message('密码错误');
        }

        Session::set('admin_id',$model_admin->id);

        return json_message();
    }
}
