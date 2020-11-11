<?php

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class UploadFiles extends Model
{
  //
  use SoftDelete;

  protected $defaultSoftDelete = 0;

  public function getSrcAttr()
  {
    return \get_source_link($this->getData('save_name'));
  }

  public function getTypeTitleAttr()
  {
    return \config('upload_type.' . $this->getData('type'));
  }

  public function getUsedTimeAttr($value)
  {
    if ($value == 0) {
      return '未使用';
    }

    return date('Y-m-d H:i:s', $value);
  }
  public function getDeleteTimeAttr($value)
  {
    if ($value == 0) {
      return '未删除';
    }

    return date('Y-m-d H:i:s', $value);
  }
  public function getClearTimeAttr($value)
  {
    if ($value == 0) {
      return '未清除';
    }

    return date('Y-m-d H:i:s', $value);
  }

  public function getStatusAttr($value, $data)
  {
    if ($data['used_time'] == 0) {
      return '未使用(仅供预览)';
    }

    if ($data['delete_time'] > 0) {
      return '已删除';
    }

    if ($data['clear_time'] > 0) {
      return '已清除';
    }

    return '使用中';
  }

  public function getFileSizeAttr($value)
  {
    return format_size($value);
  }
}
