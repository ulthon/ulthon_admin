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

        if(empty($value)){
            return '/static/images/avatar.png';
        }

        return \get_source_link($value);
    }

    public function getGroupAttr()
    {
        if(empty($this->getData('group_id'))){
            return [];
        }

        return AdminGroup::where('id',$this->getData('group_id'))->cache(60)->find();
    }
    
}
