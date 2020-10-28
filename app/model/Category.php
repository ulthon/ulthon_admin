<?php

declare(strict_types=1);

namespace app\model;

use think\facade\Config;
use think\Model;

/**
 * @mixin think\Model
 */
class Category extends Model
{
  //

  public static $allCategory = [];


  /**
   * 获取指定id下的所有分类
   *
   * @param string $id
   * @return void
   */
  public static function getListLevel($id = '',$type = 1)
  {

    if(empty(self::$allCategory)){

      $model_list = Category::where('type',$type)->order('sort asc')->select();
      self::$allCategory = array2level($model_list,0,0);
    }

    if(!empty($id)){
      $list = [];
      $in_category = [$id];
      foreach (self::$allCategory as $category) {
        if(in_array($category->pid,$in_category)){
          $list[] = $category;
          $in_category[] = $category->id;
        }
      }

      return $list;
    }

    return self::$allCategory;
  }


  public function getTitleImgAttr($value)
  {
    
    return get_source_link($value);
  }

  public function posts()
  {
    return $this->hasMany(PostCategory::class,'category_id');
  }

  /**
   * 返回的对应的post的模型
   *
   * @return void
   */
  public function getPostsModelListAttr()
  {
    $list_post_category = $this->getAttr('posts');

    $list_post = [];

    foreach ($list_post_category as $list_post_category) {
      array_push($list_post,$list_post_category->post);
    }

    return $list_post;
  }

  /**
   * 返回的对应post的数据,性能比模型要高.
   *
   * @return void
   */
  public function getPostsListAttr()
  {
    $list_post_category = $this->getAttr('posts');
    
    $list_post = array_column($list_post_category->append(['post'])->toArray(),'post');

    return $list_post;
  }

  public function getTplNameAttr($value)
  {
    return Config::get('view_type.category.'.$value);
  }

  public function getModelParentAttr()
  {
    $pid = $this->getData('pid');

    if($pid == 0){
      return $this;
    }
    return Category::where('id',$pid)->find();
  }

  // 返回除自身以外的其他的同级同类的分类
  public function getModelSiblingsAttr()
  {
    return Category::where('pid',$this->getData('pid'))
    ->where('level',$this->getData('level'))
    ->where('id','<>',$this->getData('id'))
    ->select();
  }

  /**
   * 获取同一个父元素的分类,包含自身
   *
   * @return void
   */
  public function getModelSameParentAttr()
  {
    return Category::where('pid',$this->getData('pid'))
    ->where('level',$this->getData('level'))
    ->select();
  }

}
