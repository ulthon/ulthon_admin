<?php
declare (strict_types = 1);

namespace app\common\command\admin;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Version extends Command
{

    const VERSION = 'v2.0.4';

    const COMMENT = [
        '自动生成CURD导出生成关联查询',
    ];

    protected function configure()
    {
        // 指令配置
        $this->setName('admin:version')
            ->setDescription('查看当前ulthon_admin的版本号');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->info('当前版本号为：'.$this::VERSION);

        $output->writeln('当前的修改说明:');
        $output->writeln('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
        
        foreach ($this::COMMENT as  $comment) {
            $output->info($comment);
        }
        $output->writeln('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

        $output->highlight('代码托管地址：https://gitee.com/ulthon/ulthon_admin');
        $output->highlight('开发文档地址：http://doc.ulthon.com/home/read/ulthon_admin/home.html');
        



    }
}
