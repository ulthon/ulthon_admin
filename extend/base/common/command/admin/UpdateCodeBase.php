<?php

declare(strict_types=1);

namespace base\common\command\admin;

use app\admin\service\AdminUpdateCodeService;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class UpdateCodeBase extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('admin:update:code')
            ->addOption('update-version', null, Option::VALUE_REQUIRED, '按指定版本的规则更新现有代码')
            ->setDescription('the admin:update:code command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出

        $update_version = $input->getOption('update-version');

        if(is_null($update_version)){
            $output->error('请指定更新的版本号');
            return;
        }

        $update_service = new AdminUpdateCodeService();
        $update_service->input = $input;
        $update_service->output = $output;
        $update_service->update($update_version);


    }
}
