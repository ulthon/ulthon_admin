<?php

declare(strict_types=1);

namespace base\common\command\admin;

use app\admin\service\AdminUpdateService;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class UpdateBase extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('admin:update')
            ->addOption('reinstall', null, Option::VALUE_NONE, '重装版本')
            ->setDescription('the admin:update command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('admin:update');

        $update_service = new AdminUpdateService();
        $update_service->input = $input;
        $update_service->output = $output;
        $update_service->update();
    }
}
