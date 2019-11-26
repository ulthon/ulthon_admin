<?php
namespace app;

use app\model\UploadFiles as AppUploadFiles;
use think\facade\Filesystem;

class UploadFiles  
{

    public static function add()
    {
        return new AppUploadFiles();
    }

    public static function create($data,$allowFiled = [],$replace = false)
    {
        return AppUploadFiles::create($data,$allowFiled,$replace);
    }

    public static function use($save_name)
    {
        return AppUploadFiles::where('save_name',$save_name)->update([
            'used_time'=>time(),
            'status'=>1
        ]);
    }

    public static function delete($save_name)
    {
        return AppUploadFiles::where('save_name',$save_name)->update([
            'delete_time'=>time(),
            'status'=>2
        ]);
    }

    public static function clear($id)
    {
        $model_file = AppUploadFiles::withTrashed()->find($id);

        $model_file->clear_time = time();
        $model_file->status = 3;

        $model_file->save();

        return Filesystem::delete($model_file->getData('save_name'));
    }
}
