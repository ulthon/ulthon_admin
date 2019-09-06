<?php

namespace app\admin\controller;

use app\model\AdminPermission as AppAdminPermission;
use think\facade\Cache;
use think\facade\View;
use think\Request;

class AdminPermission extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        $list = AppAdminPermission::order('app,controller,action')->paginate();

        View::assign('list',$list);

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
        $post_data = $request->post();

        $model_permission = AppAdminPermission::find($id);

        if(isset($post_data['url'])){
            $url_info = \explode('/',$post_data['url']);

            $post_data['app'] = $url_info[0];
            $post_data['controller'] = $url_info[1];
            $post_data['action'] = $url_info[2];
        }

        $model_permission->data($post_data);

        $model_permission->save();

        Cache::delete('logged_admin_permission');

        return json_message();
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
        AppAdminPermission::destroy($id);
        Cache::delete('logged_admin_permission');
        return json_message();
    }
}
