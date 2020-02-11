<?php

namespace app\admin\controller;

use app\model\User as AppUser;
use app\UploadFiles;
use think\Request;
use think\facade\View;
use think\helper\Str;

class User extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        $list = AppUser::paginate();

        View::assign('list',$list);
        return View::fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //

        return View::fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
        $post_data = $this->request->post();

        $admin_model = AppUser::where('account',$post_data['account'])->find();

        if(!empty($admin_model)){
            $this->error('用户已存在');
        }

        if(empty($post_data['password'])){
            $post_data['password'] = '123456';
        }

        if(!empty($post_data['avatar'])){
            UploadFiles::use($post_data['avatar']);
        }


        $post_data['salt'] = Str::random(6);

        $post_data['password'] = md5($post_data['password'].$post_data['salt']);

        AppUser::create($post_data);

        $this->success('添加成功','index');

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //


        $model_user = AppUser::find($id);

        View::assign('user',$model_user);

        return View::fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
        $post_data = $this->request->post();

        $model_user = AppUser::find($id);

        if(!empty($post_data['password'])){
            $post_data['salt'] = Str::random(6);

            $post_data['password'] = md5($post_data['password'].$post_data['salt']);
        }else{
            unset($post_data['password']);
        }

        if($post_data['avatar'] != $model_user->getData('avatar')){
            UploadFiles::delete($model_user->getData('avatar'));
            UploadFiles::use($post_data['avatar']);
        }

        $model_user->save($post_data);

        $this->success('修改成功','index');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //

        $model_user = AppUser::find($id);

        UploadFiles::delete($model_user->getData('avatar'));

        $model_user->delete();

        return json_message();

    }
}
