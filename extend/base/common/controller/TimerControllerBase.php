<?php

namespace base\common\controller;

use app\common\controller\ToolsController;
use think\facade\Cache;

class TimerControllerBase extends ToolsController
{
    protected $frequency = null;

    public function initialize()
    {
        parent::initialize();

        if (is_int($this->frequency)) {
            $this->protectVisit($this->frequency);
        }
    }

    protected function protectVisit(int $frequency)
    {
        $cache_tag = 'timer_protect';

        $cache_key = 'timer_protect_' . md5($this->request->url());

        $last_exec_time = Cache::get($cache_key, 0);

        if ($last_exec_time >= time() - $frequency) {
            return $this->error('请不要频繁请求');
        }

        Cache::tag($cache_tag)->set($cache_key, time());
    }
}
