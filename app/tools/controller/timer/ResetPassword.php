<?php

declare(strict_types=1);

namespace app\tools\controller\timer;

use app\common\controller\ToolsController;
use think\facade\Console;

class ResetPassword extends ToolsController
{
    public function do()
    {
        $output = Console::call('admin:resetPassword', [
            '--password=123456'
        ]);

        return $output->fetch();
    }
}
