<?php

declare(strict_types=1);

namespace app\common\command\admin;

use app\common\tools\PathTools;
use CzProject\GitPhp\Git;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;

class Update extends Command
{
    public const REPO = 'https://gitee.com/ulthon/ulthon_admin.git';

    protected function configure()
    {
        // 指令配置
        $this->setName('admin:update')
            ->setDescription('the admin:update command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('admin:update');

        $this->cleanWorkpaceDir();

        $current_version = Version::VERSION;

        $current_version_dir = App::getRuntimePath() . '/update/' . $current_version;

        $last_version_dir = App::getRuntimePath() . '/update/last';

        $version_file_regx = "/\bconst VERSION\s*=\s*'[\d\.a-z]+'/";

        $last_version_git = new Git();
        $output->writeln('下载最新代码');
        $last_version_repo = $last_version_git->cloneRepository(self::REPO, $last_version_dir);
        $last_version_file = $last_version_dir . '/app/common/command/admin/Version.php';
        $last_version_str = file_get_contents($last_version_file);
        preg_match($version_file_regx, $last_version_str, $matches);

        $matched_version_str = $matches[0];

        $last_version = str_replace('const VERSION = ', '', $matched_version_str);
        $last_version = str_replace('\'', '', $last_version);

        // if ($last_version == $current_version) {
            //     $output->writeln('当前代码为最新版本，无需更新');
            //     $this->cleanWorkpaceDir();
            //     return;
        // }

        // 将最新代码切换到最新版本
        $last_version_repo->checkout($last_version);

        $current_version_git = new Git();
        $output->writeln('获取当前版本代码');
        $current_version_repo = $current_version_git->cloneRepository(self::REPO, $current_version_dir);
        $output->writeln('切换版本' . $current_version);
        $current_version_repo->checkout($current_version);

        // 获取当前版本需要跳过的文件
        $current_version_update_config = include $current_version_dir . '/config/update.php';
        // 获取当前版本要替换的文件

        $current_version_filesystem = new Filesystem(new LocalFilesystemAdapter($current_version_dir));
        $list_files = $current_version_filesystem->listContents('/', Filesystem::LIST_DEEP);
        // dump($list_files);

        // 对比现在的代码，检查是否有定制修改
        // 有定制修改则退出

        // 获取最新版本需要跳过的文件
        // 获取最新版本要替换的文件

        // 删除当前版本要替换的文件
        // 将最新版本要替换的文件替换到项目代码中

        // 更新完成
    }

    protected function cleanWorkpaceDir()
    {
        $dir = App::getRuntimePath() . '/update/';

        $this->output->writeln('清理目录 ' . $dir);
        PathTools::removeDir($dir);
    }
}
