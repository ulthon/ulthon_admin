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

  public function setPublishTimeAttr($value)
  {
    return strtotime($value);
  }
  public function getPublishTimeTextAttr()
  {

    $value = $this->getData('publish_time');
    return date('Y-m-d',$value);
  }
  public function getPublishTimeDatetimeAttr()
  {

    $value = $this->getData('publish_time');
    return date('Y-m-d H:i:s',$value);
  }

  public function getCategorysListAttr()
  {
    $list_post_categorys = $this->getAttr('categorys');

    $list = array_column($list_post_categorys->append(['category'])->toArray(),'category');

    $list = array2level($list,0,0);

    return $list;
  }

  public function getTagsListAttr()
  {
    $list_post_tags = $this->getAttr('tags');

    $list = array_column($list_post_tags->append(['tag'])->toArray(),'tag');

    return $list;
  }

  public function getDescShortAttr()
  {
    $desc = $this->getData('desc');

    if(strlen($desc) > 100){
      $desc = mb_substr($desc,0,100).'...';
    }

    return $desc;
  }

  public function getDescListAttr()
  {
    $desc = $this->getData('desc');

    if(empty($desc)){
      return '';
    }
    $list = explode("\n", $desc);

    return $list;
  }

  public function getDescHtmlAttr()
  {
    $desc = $this->getData('desc');

    if(empty($desc)){
      return '';
    }

    return str_replace("\n",'<br>',$desc);
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
  public function setContentHtmlAttr($value)
  {
    return trim($value);
  }

  public function getContentAttr($value)
  {
    return json_decode($value,true);
  }

  public function getPosterAttr($value)
  {
    if(empty($value)){
      $value = '/static/images/avatar.png';
    }

    return get_source_link($value);
  }
}
