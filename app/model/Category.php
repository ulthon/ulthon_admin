<?php

declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Category extends Model
{
  //

  public static function getListLevel()
  {
    $model_list = Category::select();

    // return $model_list;
    return array2level($model_list,0,0);
  }
}
