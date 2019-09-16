<?php

namespace app\model;

use think\Model;

/**
 * 权限表
 * @mixin think\Model
 */
class AdminPermission extends Model
{
    //

    public function getIsLogAttr($value)
    {
        $status = [
            0=>'不记录',
            1=>'记录',
        ];

        return $status[intval($value)];
    }

    public function getNameAttr($value)
    {
        if(empty($value)){
            $value = $this->getData('app').'/'.$this->getData('controller').'/'.$this->getData('action');
        }

        return $value;
    }

    
}
