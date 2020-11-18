<?php

namespace app;

use app\model\UploadFiles as AppUploadFiles;
use League\Flysystem\Util\MimeType;
use think\facade\Filesystem;
use think\facade\Config;
use think\File;

class UploadFiles
{

  public static function add()
  {
    return new AppUploadFiles();
  }

  public static function create($data, $allowFiled = [], $replace = false)
  {
    return AppUploadFiles::create($data, $allowFiled, $replace);
  }

  public static function use($save_name)
  {
    return AppUploadFiles::where('save_name', $save_name)->update([
      'used_time' => time(),
      'status' => 1
    ]);
  }

  public static function delete($save_name)
  {
    return AppUploadFiles::where('save_name', $save_name)->update([
      'delete_time' => time(),
      'status' => 2
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

  public static function save(Request $request)
  {

    $type = $request->param('type');
    if (empty($type)) {
      return json_message('缺少类型参数');
    }

    $file = request()->file('file');

    $file_extension = $file->extension();

    if ($file_extension == 'php') {
      return json_message('上传文件异常');
    }

    $file_path = $file->getRealPath();

    $file_content = file_get_contents($file_path);

    if (strpos($file_content, '<?php') !== false) {
      return json_message('上传文件异常');
    }

    if (empty($file)) {
      return json_message('上传失败');
    }

    $dir_name = $request->param('dir', $type);
    try {
      $model_file = self::saveFile($file, $type, $dir_name);
      return json_message($model_file->append(['src'])->toArray());
    } catch (\Throwable $th) {
      return json_message($th->getMessage());
    }
  }

  public static function saveFile(File $file, $type, $dir_name = null)
  {
    if (is_null($dir_name)) {
      $dir_name = $type;
    }
    $model_file = UploadFiles::add();
    $model_file->file_name = $file->getFilename();
    $model_file->mime_type = $file->getMime();
    $model_file->ext_name = $file->extension();
    $model_file->file_size = $file->getSize();
    $model_file->file_md5 = $file->md5();
    $model_file->file_sha1 = $file->sha1();
    $model_file->create_time = time();
    $model_file->type = $type;

    $model_file->save_name = Filesystem::putFile('upload/' . $dir_name, $file, 'uniqid');
    $model_file->save();
    return $model_file;
  }

  public static function saveUrlFile($url, $type)
  {
    $file_data = geturl($url);

    $mime_type = MimeType::detectByContent($file_data);
    $ext_name = array_search($mime_type, MimeType::getExtensionToMimeTypeMap());
    $temp_file = tempnam(app()->getRuntimePath(), 'url_save_') . '.' . $ext_name;
    file_put_contents($temp_file, $file_data);
    $file = new File($temp_file);
    $model_file = self::saveFile($file, $type);
    unlink($temp_file);
    return $model_file;
  }
}
