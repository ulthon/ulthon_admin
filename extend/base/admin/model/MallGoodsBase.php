<?php

namespace base\admin\model;

use app\admin\model\MallTag;
use app\common\model\TimeModel;

class MallGoodsBase extends TimeModel
{
    protected $deleteTime = 'delete_time';

    public function cate()
    {
        return $this->belongsTo('app\admin\model\MallCate', 'cate_id', 'id');
    }

    public function getTagListTitleAttr()
    {
        $tags = $this->getAttr('tag');

        $list_tag = MallTag::whereIn('id', $tags)->column('title');

        return $list_tag;
    }
}
