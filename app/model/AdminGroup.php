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
}
