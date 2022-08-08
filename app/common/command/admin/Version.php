<?php

declare(strict_types=1);

namespace app\common\command\admin;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;

class Version extends Command
{

    const VERSION = 'v2.0.12';

    const COMMENT = [
        '表格新增存储容量格式化模板',
        '优化图片扩展名',
        '优化表格宽度自动计算',
        '优化核心库的url生成助手函数',
        '增加titleField的回调用法',
        '默认关闭CSRF配置项',
        '增加表格合计统计例子',
        '新增CURD临时生成模式',
        '表格新增if属性',
        '表格搜素searchOp新增none用法',
        '去除部分技术债务',
    ];

    protected function configure()
    {
        // 指令配置
        $this->setName('admin:version')
            ->addOption('push-tag', null, Option::VALUE_NONE, '使用git命令生成tag并推送')
            ->setDescription('查看当前ulthon_admin的版本号');
    }

    protected function execute(Input $input, Output $output)
    {

        // 指令输出
        $output->info('当前版本号为：' . $this::VERSION);

        $output->writeln('当前的修改说明:');
        $output->writeln('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

        foreach ($this::COMMENT as  $comment) {
            $output->info($comment);
        }
        $output->writeln('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

        $output->highlight('代码托管地址：https://gitee.com/ulthon/ulthon_admin');
        $output->highlight('开发文档地址：http://doc.ulthon.com/home/read/ulthon_admin/home.html');

        $is_push_tag = $input->hasOption('push-tag');

        if ($is_push_tag) {
            $output->writeln('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
            
            $version = $this::VERSION;
            $comment = implode(';', $this::COMMENT);
            $output->info('生成标签：' . $version);
            $output->info('标签描述：' . $comment);
            exec("git tag -a $version -m \"$comment\"");
            $output->info('推送到远程仓库');
            exec("git push --tags");
        }
    }
}
