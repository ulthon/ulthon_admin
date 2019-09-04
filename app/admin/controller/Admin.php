<?php

namespace app\admin\controller;

use app\model\Admin as AppAdmin;
use app\UploadFiles as AppUploadFiles;
use think\facade\View;

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
            AppUploadFiles::delete($model_admin->getData('avatar'));
            AppUploadFiles::use($post_data['avatar']);
        }


        $model_admin->data($post_data);

        $model_admin->save();

        return $this->success('保存成功','Admin/edit');
    }

    public function index()
    {

        $admin_list = AppAdmin::where('id','<>',1)->paginate();
        View::assign('list',$admin_list);
        return View::fetch();
    }

    public function create()
    {
        return View::fetch();
    }

    public function save()
    {
        
    }
}
