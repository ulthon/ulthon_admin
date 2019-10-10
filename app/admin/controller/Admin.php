<?php

namespace app\admin\controller;

use app\model\Admin as AppAdmin;
use app\model\AdminGroup;
use app\model\AdminLog;
use app\UploadFiles as AppUploadFiles;
use think\facade\View;
use think\helper\Str;

/**
 * 管理员账号管理
 */
class Admin extends Common
{
    /**
     * 当前登录的管理员编辑账户
     *
     * @return void
     */
    public function edit()
    {

        $model_admin = AppAdmin::find($this->adminInfo['id']);

        View::assign('admin',$model_admin);

        return View::fetch();
    }

    /**
     * 当前登录的管理员修改密码
     *
     * @return void
     */
    public function password()
    {
        return View::fetch();
    }

    /**
     * 当前登陆的管理员保存修改密码
     *
     * @return void
     */
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

    /**
     * 当前登陆的管理员更新账户
     *
     * @return void
     */
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

    /**
     * 管理员列表
     *
     * @return void
     */
    public function index()
    {

        $admin_list = AppAdmin::where('id','<>',1)->order('id desc')->paginate();
        View::assign('list',$admin_list);
        return View::fetch();
    }

    /**
     * 添加管理员账号
     *
     * @return void
     */
    public function create()
    {

        $admin_group_list = AdminGroup::select();

        View::assign('group_list',$admin_group_list);

        return View::fetch();
    }


    /**
     * 保存添加的管理员账号
     *
     * @return void
     */
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

        if(!empty($post_data['avatar'])){
            AppUploadFiles::use($post_data['avatar']);
        }

        $post_data['salt'] = Str::random(6);

        $post_data['password'] = md5($post_data['password'].$post_data['salt']);

        AppAdmin::create($post_data);

        $this->success('添加成功','index');

    }

    /**
     * 编辑管理员账号
     *
     * @param [type] $id
     * @return void
     */
    public function editAccount($id)
    {
        $model_admin = AppAdmin::find($id);
        $admin_group_list = AdminGroup::select();
        View::assign('group_list',$admin_group_list);
        View::assign('admin',$model_admin);
        return View::fetch();
    }

    /**
     * 更新管理员账号
     *
     * @return void
     */
    public function updateAccount()
    {
        $post_data = $this->request->post();

        $admin_model = AppAdmin::find($post_data['id']);

        if(!empty($post_data['password'])){
            $post_data['salt'] = Str::random(6);

            $post_data['password'] = md5($post_data['password'].$post_data['salt']);
        }else{
            unset($post_data['password']);
        }

        if($admin_model->getData('avatar') != $post_data['avatar']){
            AppUploadFiles::delete($admin_model->getData('avatar'));
            AppUploadFiles::use($post_data['avatar']);
        }
        AppAdmin::update($post_data);

        $this->success('修改成功','index');

    }

    /**
     * 管理员操作日志
     *
     * @return void
     */
    public function adminLog()
    {

        $list = AdminLog::order('id desc')->paginate(10);

        View::assign('list',$list);

        return View::fetch();
    }

    /**
     * 删除管理员
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id)
    {
        AppAdmin::destroy($id);

        
        return json_message();
    }
}
