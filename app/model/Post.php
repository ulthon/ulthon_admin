<?php

declare(strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class Post extends Model
{
  //

  public static $stausNameList = [
    0=>'不发布',
    1=>'发布'
  ];

  use SoftDelete;

  protected $defaultSoftDelete = 0;

  public function categorys()
  {
    return $this->hasMany(PostCategory::class,'post_id');
  }

  public function tags()
  {
    return $this->hasMany(PostTag::class,'post_id');
  }

  public function getStatusNameAttr()
  {
    return self::$stausNameList[$this->getData('status')];
  }

  public function setPubishTimeAttr($value)
  {
    return strtotime($value);
  }

  public function setContentAttr($value)
  {
    return json_encode($value);
  }

  public function getContentAttr($value)
  {
    return json_decode($value,true);
  }

  public function getPosterAttr($value)
  {
    if(empty($value)){
      $value = '/static/images/avatar.jpeg';
    }

    return get_source_link($value);
  }
}
