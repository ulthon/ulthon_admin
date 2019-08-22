<?php

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin think\Model
 */
class UploadFiles extends Model
{
    //
    use SoftDelete;

    protected $defaultSoftDelete = 0;

    public function getSrcAttr()
    {
        return \get_source_link($this->getData('save_name'));
    }
}
