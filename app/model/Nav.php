<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Nav extends Model
{
    //
    public function getImgAttr($value)
    {
      return get_source_link($value);
    }
}
