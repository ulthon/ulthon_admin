<?php

namespace base\common\event\AdminLoginType;

use think\facade\Env;
use think\facade\View;

class DemoEventBase
{
    public function handle()
    {
        $content = '';

        if (Env::get('adminsystem.is_demo', false)) {
            $content = View::layout(false)->fetch('login/ext/demo');
        }

        // 事件监听处理
        return [
            'view_content' => $content,
        ];
    }
}
