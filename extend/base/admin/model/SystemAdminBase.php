<?php

namespace base\admin\model;

use app\admin\model\SystemAuth;
use app\common\model\TimeModel;

class SystemAdminBase extends TimeModel
{
    protected $deleteTime = 'delete_time';

    public static $autoCache = [
        [
            'name' => 'info',
            'field' => 'id',
        ],
    ];

    public function getAuthList()
    {
        $list = (new SystemAuth())
            ->where('status', 1)
            ->column('title', 'id');

        return $list;
    }
}
