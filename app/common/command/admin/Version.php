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

    const VERSION = 'v2.0.22';

    const LAYUI_VERSION = '2.7.6';

    const COMMENT = [
        '升级ThinkPHP6.1',
        '升级第三方flysystem组件',
        '调整表格重载特性',
        '完善CURD模板',
        '修改table.operat.field特性',
        '清理技术债务',
        '增加权限菜单的权限标志',
        '修复导出方法文件名获取方式',
        '优化时间范围和数字范围的placeholder',
        '优化版本设置有效性',
        '优化表格导出按照字段字数设置宽度',
        '默认允许icon上传',
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
