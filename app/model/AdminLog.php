<?php

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class AdminLog extends Model
{
    //
    use SoftDelete;
    protected $defaultSoftDelete = 0;

    public function admin()
    {
        return $this->belongsTo('Admin','admin_id');
    }


}
