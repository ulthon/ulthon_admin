<?php

namespace app\model;

use think\Model;


/**
 * @mixin think\Model
 */
class WxPublicAccount extends Model
{
    //
    public function getHeadImgAttr($value)
    {
        return get_source_link($value);
    }

    public function getQrcodeUrlAttr($value)
    {
        return \get_source_link($value);
    }
}
