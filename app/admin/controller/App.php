<?php

namespace app\admin\controller;

use think\Request;
use think\facade\View;
use app\model\App as ModelApp;
use app\model\UploadFiles;

class App extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        $app_list = ModelApp::paginate(10);

        View::assign('app_list',$app_list);
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

        $installed_app_list = ModelApp::column('mark_id');

        $app_list = get_app_info();

        View::assign('app_list',$app_list);
        View::assign('installed_app_list',$installed_app_list);

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

        $model_app = ModelApp::where('mark_id',$post_data['mark_id'])->find();

        if(!empty($model_app)){
            return json_message('应用已存在，不能重复创建');
        }

        $model_app = new ModelApp();

        if(!empty($post_data['poster'])){
            UploadFiles::update(['userd_time'=>time()],['save_name'=>$post_data['poster']]);
        }

        if(!empty($post_data['detail'])){

            foreach ($post_data['detail'] as $key => $value) {
                if(isset($value['insert'])){
                    if(isset($value['insert']['image'])){
                        $full_save_name = $value['insert']['image'];
                        $save_name = de_source_link($full_save_name);
                        if($save_name){
                            UploadFiles::update(['used_time'=>time()],['save_name'=>$save_name]);
                        }
                    }
                }
            }
        }


        $model_app->data($post_data,true);

        $model_app->save();

        return json_message();
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
        $model_app = ModelApp::find($id);
        $app_list = get_app_info();

        View::assign('app_list',$app_list);
        View::assign('app',$model_app);

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
         //
        $post_data = $request->post();

        $model_app = ModelApp::where('id',$id)->find();

        if(empty($model_app)){
            return json_message('应用不存在');
        }

        if(!empty($post_data['poster'])){
            if($post_data['poster'] != $model_app->getData('poster')){
                UploadFiles::destroy(['save_name'=>$model_app->getData('poster')]);
                UploadFiles::update(['userd_time'=>time()],['save_name'=>$post_data['poster']]);
            }
        }

        if(!empty($post_data['detail'])){
            $image_list = [];
            $new_image_list = [];
            foreach ($model_app->detail as $key => $value) {
                if(isset($value['insert'])){
                    if(isset($value['insert']['image'])){
                        $full_save_name = $value['insert']['image'];
                        $save_name = de_source_link($full_save_name);
                        if($save_name){
                            $image_list[] = $save_name;
                        }
                    }
                }
            }
            foreach ($post_data['detail'] as $key => $value) {
                if(isset($value['insert'])){
                    if(isset($value['insert']['image'])){
                        $full_save_name = $value['insert']['image'];
                        $save_name = de_source_link($full_save_name);
                        if($save_name){
                            $new_image_list[] = $save_name;
                        }
                    }
                }
            }

            $del_image_list = array_diff($image_list,$new_image_list);

            foreach ($del_image_list as $key => $value) {
                UploadFiles::destroy(['save_name'=>$value]);
            }
            
            $add_image_list = array_diff($new_image_list,$image_list);

            foreach ($add_image_list as $key => $value) {
                UploadFiles::update(['used_time'=>time()],['save_name'=>$value]);
            }
        }


        $model_app->data($post_data,true);

        $model_app->save();

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
        $model_app = ModelApp::find($id);

        if(!empty($model_app->getData('poster'))){
            UploadFiles::udpate(['delete_time'=>time()],['save_name'=>$model_app->getData('poster')]);
        }
        
        if(!empty($model_app->getData('detail'))){
            foreach ($model_app->detail as $key => $value) {
                if(isset($value['insert'])){
                    if(isset($value['insert']['image'])){
                        $full_save_name = $value['insert']['image'];
                        $save_name = de_source_link($full_save_name);
                        if($save_name){
                            UploadFiles::update(['delete_time'=>time()],['save_name'=>$save_name]);
                        }
                    }
                }
            }
        }
        $model_app->delete();

        return json_message();
    }
}
