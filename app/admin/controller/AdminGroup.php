<?php

namespace app\admin\controller;

use app\model\AdminGroup as AppAdminGroup;
use app\model\AdminPermission;
use think\facade\View;
use think\Request;

class AdminGroup extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $list = AppAdminGroup::order('id desc')->select();
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
        $premission_list = AdminPermission::order('key')->select();

        View::assign('permission_list',$premission_list);
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
        $post_data = $request->post();
        
        $model_admin_group = AppAdminGroup::where('name',$post_data['name'])->find();

        if(!empty($model_admin_group)){
            return $this->error('分组已存在');
        }

        try {
            AppAdminGroup::create($post_data);
        } catch (\Throwable $th) {
            return $this->error('创建失败:'.$th->getMessage());
        }

        return $this->success('创建成功','index');

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

        $model_admin_group = AppAdminGroup::find($id);

        $premission_list = AdminPermission::order('key')->select();
        
        View::assign('permission_list',$premission_list);
        View::assign('admin_group',$model_admin_group);

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
        $model_admin_group = AppAdminGroup::find($id);
        if(empty($model_admin_group)){
            return $this->error('分组不存在');
        }

        $post_data = $request->post();
        
        $model_admin_group->save($post_data);

        return $this->success('修改成功','index');
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
        AppAdminGroup::destroy($id);
        $this->success('删除成功');
    }
}
