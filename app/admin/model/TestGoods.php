<?php

namespace app\admin\model;

use app\common\model\TimeModel;

class TestGoods extends TimeModel
{

    protected $name = "test_goods";

    protected $deleteTime = "delete_time";

    
    public const SELECT_LIST_STATUS = ['0'=>'正常','1'=>'禁用',];

    public const SELECT_LIST_TIME_STATUS = ['0'=>'未参加','1'=>'已开始','3'=>'已结束',];

    public const SELECT_LIST_IS_RECOMMEND = ['0'=>'不推荐','1'=>'推荐',];

    public const SELECT_LIST_SHOP_TYPE = ['taobao'=>'淘宝','jd'=>'京东',];

    
    
    public function mallCate()
    {
        return $this->belongsTo('\app\admin\model\MallCate', 'cate_id', 'id');
    }


}