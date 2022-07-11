<?php

namespace app\common\service;

use think\facade\Filesystem;
use think\facade\Validate;
use think\File;

class UploadService
{

    protected $uploadType = 'local_public';

    public function __construct($upload_type = 'local_public')
    {
        $uploadConfig = sysconfig('upload');

        empty($upload_type) && $upload_type = $uploadConfig['upload_type'];

        $this->uploadType = $upload_type;
    }

    public function validate($file, $allow_ext = null, $allow_size = null, $fail_exception = false)
    {
        $uploadConfig = sysconfig('upload');

        if (!is_null($allow_ext)) {
            $uploadConfig['upload_allow_ext'] = $allow_ext;
        }

        if (!is_null($allow_size)) {
            $uploadConfig['upload_allow_size'] = $allow_size;
        }
        
        $rule = [
            'upload_type|指定上传类型有误' => "in:{$uploadConfig['upload_allow_type']}",
            'file|文件'              => "require|file|fileExt:{$uploadConfig['upload_allow_ext']}|fileSize:{$uploadConfig['upload_allow_size']}",
        ];

        return Validate::failException($fail_exception)->check([
            'upload_type' => $this->uploadType,
            'file' => $file
        ], $rule);
    }

    public function validateException($file, $allow_ext = null, $allow_size = null)
    {
        return $this->validate($file, $allow_ext, $allow_size, true);
    }

    public function save(File $file)
    {


        $save_name = Filesystem::disk($this->uploadType)->putFile('upload', $file, function () {
            return date('Ymd') . DIRECTORY_SEPARATOR . uniqid();
        });

        $url = build_upload_url($save_name);

        return [
            'url' => $url,
            'save_name' => $save_name
        ];
    }
}
