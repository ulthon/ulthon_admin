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

    public function getUrlAttr()
    {
        return AdminPermission::where([
            'app'=>$this->getData('app'),
            'controller'=>$this->getData('controller'),
            'action'=>$this->getData('action'),
        ])->find();
    }

    public function setParamAttr($value)
    {
        return json_encode($value,JSON_UNESCAPED_UNICODE);
    }

    public function getParamAttr($value)
    {
        return \mb_substr($value,0,30);
    }
}
