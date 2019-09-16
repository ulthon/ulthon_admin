<?php

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class AdminGroup extends Model
{
    //
    use SoftDelete;
    protected $defaultSoftDelete = 0;

    public function getPermissionsAttr($value)
    {
        return \explode(',',$value);
    }

    public function setPermissionsAttr($value)
    {
        
        if(is_array($value)){
            return join(',',$value);
        }

        return $value;
        
    }
}
