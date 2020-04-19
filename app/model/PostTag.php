<?php

declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class PostTag extends Model
{
  //
  public function tag()
  {
    return $this->belongsTo(Tag::class,'tag_id');
  }

  public function post()
  {
    return $this->belongsTo(Post::class, 'post_id');
  }
}
