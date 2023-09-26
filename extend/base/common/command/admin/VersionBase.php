<?php

declare(strict_types=1);

namespace base\common\command\admin;

use think\App as ThinkApp;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class VersionBase extends Command
{
    public const VERSION = 'v2.0.47';

    public const LAYUI_VERSION = '2.8.16';

    public const COMMENT = [
        '将topthink/think-template引入到框架内；',
        '增加fetch和include的文件目录的@和@/开头的定位方法',
        '将fetch方法改为return的模式',
        '发布新版本',
    ];

    public const UPDATE_TIPS = [
        '本次调整了composer依赖，应按照新的composer 调整',
        '删除 topthink/think-template',
        '引入 psr/simple-cache>=1.0',
        '然后重新执行更新命令，删除后会自动初始化新的代码',

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
            $comment = implode(';', $this::COMMENT);
            $output->info('生成标签：' . $version);
            $output->info('标签描述：' . $comment);
            exec("git tag -a $version -m \"$comment\"");
            $output->info('推送到远程仓库');
            exec('git push --tags');
        }
    }
}
