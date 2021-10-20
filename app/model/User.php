<?php

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class User extends Model
{
    //

    use SoftDelete;

    protected $defaultSoftDelete = 0;

    public function getAvatarSrcAttr()
    {
        $value = $this->getAttr('avatar');
        if (empty($value)) {
            return '/static/images/avatar.png';
        }

        return \get_source_link($value);
    }
}
