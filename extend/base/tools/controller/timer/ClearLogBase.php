<?php

declare(strict_types=1);

namespace base\tools\controller\timer;

use app\common\controller\TimerController;
use think\facade\Db;
use think\facade\Log;

class ClearLogBase extends TimerController
{
    protected $frequency = 600;

    public function do()
    {
        Log::debug('清除3天的日志');
        Db::name('debug_log')->where('create_time', '<', time() - 60 * 60 * 24 * 3)->delete();

        return 'success';
    }
}
