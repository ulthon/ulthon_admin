<?php

namespace app\common\service;

use app\admin\model\SystemUploadfile;
use think\facade\Filesystem;
use think\facade\Validate;
use think\File;
use think\file\UploadedFile;

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

    public function url($save_name)
    {
        $url = Filesystem::disk($this->uploadType)->url($save_name);
        return $url;
    }

    public function save(File $file, string $save_name = null)
    {

        $model_file = new SystemUploadfile();

        $model_file->upload_type = $this->uploadType;
        $model_file->file_ext = strtolower($file->extension());


        if ($file instanceof UploadedFile) {

            $model_file->original_name = $file->getOriginalName();
            $model_file->mime_type = $file->getOriginalMime();
        } else {
            $model_file->original_name = $file->getFilename();
            $model_file->mime_type = $file->getMime();
        }

        $save_name = Filesystem::disk($this->uploadType)->putFile('upload', $file, function () use ($save_name) {
            if (!is_null($save_name)) {
                return $save_name;
            }
            return date('Ymd') . '/' . uniqid();
        });

        $url = $this->url($save_name);

        $model_file->url = $url;
        $model_file->save_name = $save_name;

        $model_file->sha1 = $file->sha1();


        $model_file->file_size = $file->getSize();

        $model_file->save();
        return [
            'url' => $url,
            'save_name' => $save_name
        ];
    }
}
