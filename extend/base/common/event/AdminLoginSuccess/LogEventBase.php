<?php

namespace base\common\event\AdminLoginSuccess;

use app\admin\model\SystemAdmin;
use think\facade\Log;

class LogEventBase
{
    public function handle(SystemAdmin $system_admin)
    {
        // 事件监听处理
        Log::report("admin login success,{$system_admin->username}");
    }
}
