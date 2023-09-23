<?php

namespace base\admin\model;

use app\common\model\TimeModel;

class DebugLogBase extends TimeModel
{
    protected $name = 'debug_log';

    protected $deleteTime = false;
}
