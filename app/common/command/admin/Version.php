<?php

declare(strict_types=1);

namespace app\common\command\admin;

use think\App as ThinkApp;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;

class Version extends Command
{

    const VERSION = 'v2.0.25';

    const LAYUI_VERSION = '2.7.6';

    const COMMENT = [
        '修复部分css没有正确使用缓存参数',
        '修复table的field属性的边界情况处理',
        '新增table的重载方法',
        '优化table的tabbar设置关闭的情况',
        '修复菜单管理修改排序的错误效果',
        '新增curd生成的模型支持注解部分',
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
        $output->info('当前Layui版本号为：' . $this::LAYUI_VERSION);
        $output->info('当前ThinkPHP版本号为：' . ThinkApp::VERSION);

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
            $comment = implode(";", $this::COMMENT);
            $output->info('生成标签：' . $version);
            $output->info('标签描述：' . $comment);
            exec("git tag -a $version -m \"$comment\"");
            $output->info('推送到远程仓库');
            exec("git push --tags");
        }
    }
}
