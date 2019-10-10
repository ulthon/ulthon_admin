<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\Session;
use app\model\Admin;
use app\model\AdminPermission;
use think\exception\HttpResponseException;
use think\facade\View;

class Common extends BaseController{

    public $adminInfo = null;

    public function initialize()
    {


        $admin_id = Session::get('admin_id');

        if($this->request->controller() !== 'Login'){
            
            if(empty($admin_id)){
                return $this->error('请登录','admin/Login/index');
            }
            
            $this->adminInfo = Admin::find(Session::get('admin_id'));
    
            if(empty($this->adminInfo)){
                if($this->request->controller() !== 'Login'){
                    throw new HttpResponseException(redirect('admin/Login/index'));
                }
            }

            if(!empty($this->adminInfo['group'])){

                $current_access_info = [
                    'app'=>app('http')->getName(),
                    'controller'=>request()->controller(),
                    'action'=>request()->action()
                ];

                $model_permission = AdminPermission::where($current_access_info)->find();

                if(!empty($model_permission)){
                    if(!in_array($model_permission->id,$this->adminInfo->group->permissions)){
                        return $this->error('您没有访问权限');
                    }
                }
            }



        }

        View::assign('admin',$this->adminInfo);

    }
}