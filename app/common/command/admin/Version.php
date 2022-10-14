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

    const VERSION = 'v2.0.20';

    const LAYUI_VERSION = '2.7.6';

    const COMMENT = [
        '优化多处细节',
        '优化生成数据库迁移代码',
        '优化tableData组件',
        '优化表格链接模板',
        '增加删除临时代码命令',
        '修复data-images放大顺序问题',
        '修复编辑菜单导致丢失pid问题',
        '优化跳转页面对PHP8.1的兼容',
        '新增表格列的valueParser设置',
        '新增全局复制事件监听',
        '新增表格复制模板',
        '完善url拼装',
        '新增data-request的参数设置',
        '增加兼容php8.1的函数',
        '优化管理员添加设置默认值头像',
        '增加控制器设置导出文件的文件名',
        '优化getDataBrage支持嵌套读取',
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
            $comment = implode("\n", $this::COMMENT);
            $output->info('生成标签：' . $version);
            $output->info('标签描述：' . $comment);
            exec("git tag -a $version -m \"$comment\"");
            $output->info('推送到远程仓库');
            exec("git push --tags");
        }
    }
}
