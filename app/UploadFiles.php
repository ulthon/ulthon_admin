<?php
namespace app;

use app\model\UploadFiles as AppUploadFiles;

class UploadFiles  
{
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

    public static function clear()
    {
        
    }
}
