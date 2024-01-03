<?php

namespace base\common\command;

use app\common\interface\test\CommandTestInterface;
use app\common\service\test\LogTestService;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class TestBase extends Command
{
    protected $program = [
        LogTestService::NAME => LogTestService::class,
    ];

    protected function configure()
    {
        // 指令配置
        $this->setName('test')
            ->addArgument('program', Option::VALUE_REQUIRED, '测试项目')
            ->setDescription('the admin:update command');
    }

    protected function execute(Input $input, Output $output)
    {
        $program = $input->getArgument('program');

        if (empty($program)) {
            $output->writeln('请输入测试项目');

            return;
        }

        if (!isset($this->program[$program])) {
            $output->writeln('测试项目不存在');

            return;
        }

        $class = $this->program[$program];

        $run = $class::RUN;

        $instance = new $class();

        $output->writeln('测试项目名称：' . $instance->getName());
        $output->writeln('测试项目描述：' . $instance->getDesc());

        if ($instance instanceof CommandTestInterface) {
            $instance->setInput($input);
            $instance->setOutput($output);
        }

        $instance->$run();
    }
}
