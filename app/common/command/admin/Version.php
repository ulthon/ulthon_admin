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

    const VERSION = 'v2.0.17';

    const LAYUI_VERSION = '2.7.6';

    const COMMENT = [
        '增加trueHide的回调用法',
        '增加trueHide的权限检测用法',
        '明确指定生产环境的日志级别',
        '增加图片的webp格式上传',
        '增加需要编译的scss文件说明',
        '调整后台布局细节',
        '优化商城导出',
        '表格的reload修改为reloadData',
        '升级layui到2.7.6',
        '优化version显示',
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
            exec("git push --tags");
        }
    }
}
