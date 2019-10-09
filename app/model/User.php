<?php

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class User extends Model
{
    //

    public function getAvatarAttr($value)
    {
        if(empty($value)){
            return '/static/images/avatar.jpeg';
        }

        return \get_source_link($value);
    }
}
