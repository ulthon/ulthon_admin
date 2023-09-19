<?php

declare(strict_types=1);

namespace app\common\command\admin;

use app\common\tools\PathTools;
use CzProject\GitPhp\Git;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\StorageAttributes;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;
use think\facade\Env;

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

        $output->writeln('下载最新代码');
        $last_version_git = new Git();

        $last_version_repo = $last_version_git->cloneRepository(self::REPO, $last_version_dir);

        $tags = $last_version_repo->getTags();

        $update_level = Env::get('adminsystem.update_level', 'production');

        if ($update_level == 'production') {
            $tags = array_filter($tags, function ($value) {
                if (strpos($value, '-')) {
                    return false;
                }

                return true;
            });
        }

        usort($tags, function ($a, $b) {
            return version_compare($a, $b);
        });

        $last_version = $tags[count($tags) - 1];

        if ($last_version == $current_version) {
            $output->writeln('当前版本为最新版本');
            $this->cleanWorkpaceDir();

            return;
        }

        // 将最新代码切换到最新版本，因为最新的提交可能没有发布版本
        $output->writeln('切换最新代码的最新版本');
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
        $current_version_list_files = $current_version_filesystem->listContents('', Filesystem::LIST_DEEP)
        ->filter(function (StorageAttributes $attributes) use ($current_version_update_config) {
            if ($attributes->isDir()) {
                return false;
            }

            $path = $attributes->path();

            $skip_files = $current_version_update_config['skip_files'] ?? [];

            if (in_array($path, $skip_files)) {
                return false;
            }

            $skip_dir = $current_version_update_config['skip_dir'] ?? [];
            $skip_dir[] = '.git';

            foreach ($skip_dir as $dir) {
                if (str_starts_with($path, $dir)) {
                    return false;
                }
            }

            return true;
        })
        ->map(fn (StorageAttributes $attributes) => $attributes->path())
        ->toArray();

        // 对比现在的代码，检查是否有定制修改

        $output->writeln('对比源码是否被定制');
        $now_dir = App::getRootPath();

        $changed_files = [];

        foreach ($current_version_list_files as $file_path) {
            $now_file_path = $now_dir . '/' . $file_path;

            $current_version_file_path = $current_version_dir . '/' . $file_path;

            if (!PathTools::compareFiles($now_file_path, $current_version_file_path)) {
                $changed_files[] = $file_path;
            }
        }

        // 有定制修改则退出

        if (!empty($changed_files)) {
            $output->warning('无法自动更新，以下文件被定制，请还原或手动升级:');

            foreach ($changed_files as $file_path) {
                $output->warning($file_path);
            }

            return;
        }

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
