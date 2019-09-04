<?php

namespace app\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Admin extends Model
{
    //

    public function getAvatarAttr($value)
    {
        return \get_source_link($value);
    }

    public function getGroupAttr()
    {
        if(empty($this->getData('group_id'))){
            return '未分组';
        }
    }
}
