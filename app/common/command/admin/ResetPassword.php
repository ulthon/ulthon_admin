<?php

declare(strict_types=1);

namespace app\common\command\admin;

use app\admin\model\SystemAdmin;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class ResetPassword extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('admin:resetPassword')
            ->setDescription('the admin:resetPassword command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('admin:resetPassword');


        $model_admin = SystemAdmin::where('username', 'admin')->find();
        if (empty($model_admin)) {
            $output->writeln('管理员不存在');
            return false;
        }

        $model_admin->save([
            'password' => password(123456)
        ]);

        $output->writeln('修改成功');
    }
}
