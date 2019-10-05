<?php

namespace app\admin\controller;

use app\model\Admin as AppAdmin;
use app\model\AdminGroup;
use app\model\AdminLog;
use app\UploadFiles as AppUploadFiles;
use think\facade\View;
use think\helper\Str;

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

        $admin_group_list = AdminGroup::select();

        View::assign('group_list',$admin_group_list);

        return View::fetch();
    }

    public function save()
    {
        $post_data = $this->request->post();

        $admin_model = AppAdmin::where('account',$post_data['account'])->find();

        if(!empty($admin_model)){
            $this->error('管理员已存在');
        }

        if(empty($post_data['password'])){
            $post_data['password'] = '123456';
        }


        $post_data['salt'] = Str::random(6);

        $post_data['password'] = md5($post_data['password'].$post_data['salt']);

        AppAdmin::create($post_data);

        $this->success('添加成功','index');

    }

    public function editAccount($id)
    {
        $model_admin = AppAdmin::find($id);
        $admin_group_list = AdminGroup::select();
        View::assign('group_list',$admin_group_list);
        View::assign('admin',$model_admin);
        return View::fetch();
    }

    public function updateAccount()
    {
        $post_data = $this->request->post();

        if(!empty($post_data['password'])){
            $post_data['salt'] = Str::random(6);

            $post_data['password'] = md5($post_data['password'].$post_data['salt']);
        }else{
            unset($post_data['password']);
        }

        AppAdmin::update($post_data);

        $this->success('修改成功','index');

    }

    public function adminLog()
    {

        $list = AdminLog::order('id desc')->paginate(10);

        View::assign('list',$list);

        return View::fetch();
    }

    public function delete($id)
    {
        AppAdmin::destroy($id);

        
        return json_message();
    }
}
