<?php

namespace base\admin\model;

use app\common\model\TimeModel;

class SystemQuickBase extends TimeModel
{
    protected $deleteTime = 'delete_time';

    public static $autoCache = [
        [
            'name' => 'welcome_list',
        ],
    ];
}
