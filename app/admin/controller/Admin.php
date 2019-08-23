<?php

namespace app\admin\controller;

use app\model\Admin as AppAdmin;
use app\model\UploadFiles;
use think\facade\View;
use think\Request;

class Admin extends Common
{
    public function edit()
    {


        $model_admin = AppAdmin::find($this->adminInfo['id']);

        View::assign('admin',$model_admin);

        return View::fetch();
    }

    public function password()
    {
        return View::fetch();
    }

    public function passwordUpdate()
    {

        $post_data = $this->request->post();

        if(empty($post_data['new_password'])){
            return $this->error('新密码不能为空');
        }
        $model_admin = AppAdmin::find($this->adminInfo['id']);

        if(md5($post_data['original_password'].$model_admin->getData('salt')) != $model_admin->getData('password')){
            return $this->error('原密码错误');
        }

        if($post_data['new_password'] != $post_data['check_password']){
            return $this->error('新密码与确认密码不一致');
        }


        $model_admin->password = md5($post_data['new_password'].$model_admin->getData('salt'));

        $model_admin->save();

        return $this->success('修改成功');

    }

    public function update()
    {
        $post_data = $this->request->post();
        $model_admin = AppAdmin::find($this->adminInfo['id']);

        if($model_admin->getData('avatar') != $post_data['avatar']){
            UploadFiles::destroy(['save_name'=>$model_admin->getData('avatar')]);
            UploadFiles::update(['used_time'=>time()],['save_name'=>$post_data['avatar']]);
        }


        $model_admin->data($post_data);

        $model_admin->save();

        return $this->success('保存成功','Admin/edit');
    }
}
