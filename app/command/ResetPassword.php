<?php

declare(strict_types=1);

namespace app\command;

use app\model\Admin;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\helper\Str;

class ResetPassword extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('reset_password')
            ->setDescription('the reset_password command');        
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        
        $account['salt'] = Str::random(6);
        $account['password'] = md5('123456'.$account['salt']);
        Admin::update($account,1);
    	$output->writeln('重置密码成功');
    }
}
