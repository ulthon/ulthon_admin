<?php

namespace app\api\controller;

use think\Request;
use think\facade\Filesystem;
use think\facade\Config;
use app\BaseController;
use app\UploadFiles as AppUploadFiles;

class Files extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $type = $request->param('type');
        if(empty($type)){
            return json_message('缺少类型参数');
        }
        
        $file = request()->file('file');

        $file_extension = $file->extension();
        
        if($file_extension == 'php'){
            return json_message('上传文件异常');
        }

        $file_path = $file->getRealPath();

        $file_content = file_get_contents($file_path);

        if(strpos($file_content,'<?php') !== false){
            return json_message('上传文件异常');
        }

        if(empty($file)){
            return json_message('上传失败');
        }

        $dir_name = $request->param('dir','data');
        $model_file = AppUploadFiles::add();
        $model_file->file_name = $file->getOriginalName();
        $model_file->mime_type = $file->getOriginalMime();
        $model_file->ext_name = $file->extension();
        $model_file->file_size = $file->getSize();
        $model_file->file_md5 = $file->md5();
        $model_file->file_sha1 = $file->sha1();
        $model_file->create_time = time();
        $model_file->type = $type;
        try {
            $model_file->save_name = Filesystem::putFile('upload/'.$dir_name,$file,'uniqid');
            $model_file->save();
            return json_message($model_file->append(['src'])->toArray());
        } catch (\Throwable $th) {
            return json_message($th->getMessage());
        }
        
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
    }
}
