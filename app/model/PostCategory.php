<?php

declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class PostCategory extends Model
{
  //
  public function post()
  {
    return $this->belongsTo(Post::class, 'post_id');
  }

  public function category()
  {
    return $this->belongsTo(Category::class,'category_id');
  }
}
