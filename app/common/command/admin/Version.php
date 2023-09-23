<?php

declare(strict_types=1);

namespace app\common\command\admin;

use think\App as ThinkApp;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Version extends Command
{
    public const VERSION = 'v2.0.41';

    public const LAYUI_VERSION = '2.8.16';

    public const COMMENT = [
        '调整主要代码的扩展方式；',
        '调整更新逻辑',
        '发布新版本',
    ];

    public const UPDATE_TIPS = [
        '本次调整了append类型的更新文件列表，',
        '建议删除以下目录的内容，再重新获取更新，以便初始化相关类，但如果您定制了相关文件，请不要删除，可以参考最新版本的代码调整文件',
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
