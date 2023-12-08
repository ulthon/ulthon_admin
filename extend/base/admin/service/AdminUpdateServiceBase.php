<?php

namespace base\admin\service;

use app\common\command\admin\Version;
use app\common\tools\PathTools;
use CzProject\GitPhp\Git;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\StorageAttributes;
use think\console\Input;
use think\console\Output;
use think\facade\App;
use think\facade\Env;

class AdminUpdateServiceBase
{
    public const REPO = 'https://gitee.com/ulthon/ulthon_admin.git';

    /**
     * @var Input
     */
    public $input;

    /**
     * @var Output
     */
    public $output;

    public function __construct()
    {
    }

    public function update()
    {
        $output = $this->output;
        $input = $this->input;

        $this->cleanWorkpaceDir();

        $current_version = Version::VERSION;

        $current_version_dir = App::getRuntimePath() . '/update/' . $current_version;
        $last_version_dir = App::getRuntimePath() . '/update/last';
        $now_dir = App::getRootPath();

        $output->writeln('获取最新代码');
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

            if ($input->hasOption('reinstall')) {
                $output->writeln('重装代码');
            } else {
                $this->cleanWorkpaceDir();

                return;
            }
        }

        // 将最新代码切换到最新版本，因为最新的提交可能没有发布版本
        $output->writeln('切换最新代码的最新版本');
        $last_version_repo->checkout($last_version);

        $current_version_git = new Git();
        $output->writeln('获取当前版本代码');
        $current_version_repo = $current_version_git->cloneRepository(self::REPO, $current_version_dir);
        $output->writeln('切换版本' . $current_version);
        $current_version_repo->checkout($current_version);

        $output->writeln('开始比较版本差异');

        $current_version_filesystem = new Filesystem(new LocalFilesystemAdapter($current_version_dir));
        $last_version_filesystem = new Filesystem(new LocalFilesystemAdapter($last_version_dir));
        $now_filesystem = new Filesystem(new LocalFilesystemAdapter($now_dir));

        // app、config下的所有文件和根目录下的几个文件
        // 如果与当前版本一致（没有定制过），则将这种文件增加到完全跟踪列表，（新版覆盖、新版删除），
        // 否则不会覆盖和删除，但会将新文件追加到相应目录下

        // 其余的所有代码都应当完全跟踪

        // 完全跳过runtime、vendor、.git目录

        $ignore_prefix = [
            'runtime' => 'recursive',
            'vendor' => 'recursive',
            '.git' => 'recursive',
        ];

        $filter_files_function = function (StorageAttributes $attributes) use ($ignore_prefix) {
            if ($attributes->isDir()) {
                return false;
            }

            foreach ($ignore_prefix as $prefix) {
                if (str_starts_with($attributes->path(), $prefix)) {
                    return false;
                }
            }

            return true;
        };

        // 当前版本的应该被处理所有文件
        $current_version_files = $current_version_filesystem->listContents('/', true)
        ->filter($filter_files_function)
        ->map(fn (StorageAttributes $attributes) => $attributes->path())
        ->toArray();

        // 最新版本的所有文件
        $last_version_files = $last_version_filesystem->listContents('/', true)
        ->filter($filter_files_function)
        ->map(fn (StorageAttributes $attributes) => $attributes->path())
        ->toArray();

        // 本身的所有文件
        $now_files = $now_filesystem->listContents('/', true)
        ->filter($filter_files_function)
        ->map(fn (StorageAttributes $attributes) => $attributes->path())
        ->toArray();

        $changed_files = [];

        // 需要删除的文件
        $need_delete_files = array_diff($now_files, $last_version_files);

        foreach ($need_delete_files as $file_path) {
            $changed_files[$file_path] = 'delete';
        }

        // 需要增加的文件
        $need_add_files = array_diff($last_version_files, $now_files);

        foreach ($need_add_files as $file_path) {
            $changed_files[$file_path] = 'add';
        }

        // 需要更新的文件
        $need_update_files = array_intersect($now_files, $last_version_files);

        foreach ($need_update_files as $file_path) {
            $changed_files[$file_path] = 'update';
        }

        // 提示用户有一些完全跟踪的文件被修改了

        $optional_update_waring_files = [];
        $force_update_waring_files = [];

        $need_process_files = [];

        foreach ($changed_files as $file_path => $type) {
            if ($type == 'add') {
                $need_process_files[$file_path] = $type;
                continue;
            }

            $now_file_path = $now_dir . '/' . $file_path;
            $current_file_path = $current_version_dir . '/' . $file_path;
            $last_file_path = $last_version_dir . '/' . $file_path;

            // 如果现存文件和新版本一致，则无需处理
            if (PathTools::compareFiles($now_file_path, $last_file_path)) {
                continue;
            }

            // 如果现存版本和当前版本一致，则直接处理
            if (PathTools::compareFiles($now_file_path, $current_file_path)) {
                // 如果当前代码 和 当前版本 一致
                $need_process_files[$file_path] = $type;
                continue;
            }

            if ($this->testIsOptionalFiles($file_path)) {
                // 可选更新的文件发生了变化，提示用户手动维护上游信息
                $optional_update_waring_files[$file_path] = $type;
            } else {
                // 强制更新的文件被定制了，需要提醒可能会产生错误
                $force_update_waring_files[$file_path] = $type;
            }
        }

        if (!empty($optional_update_waring_files)) {
            foreach ($optional_update_waring_files as $file_path => $type) {
                $output->writeln($file_path . ' ' . $type);
            }
            $output->writeln('以上文件被您修改了，这些文件是默认的系统文件，并非您的主要业务代码，');
            $output->writeln('您可能通过扩展机制修改了以上文件来定制系统代码的逻辑');
            $output->writeln('您可能需要根据扩展规则查看系统逻辑是否发生了变化，如果发生了变化，您需要手动修改这些文件');

            $is_udpate_optinal_files = $output->confirm($input, '确定要更新吗？（建议不更新）', false);

            if ($is_udpate_optinal_files) {
                $need_process_files = array_merge($need_process_files, $optional_update_waring_files);
            }
        }

        if (!empty($force_update_waring_files)) {
            foreach ($force_update_waring_files as $file_path => $type) {
                $output->writeln($file_path . ' ' . $type);
            }
            $output->writeln('以上文件被您定制了，您不应该修改这些文件，');
            $output->writeln('但您出于某些原因修改了他们，如果继续更新，会覆盖至最新版本，');
            $output->writeln('这些改动不应该发生，继续自动升级可能会导致错误，');
            $output->writeln('建议您选择更新，然后将这些改动的逻辑通过扩展的机制重新实现');

            $is_udpate_force_files = $output->confirm($input, '确定要更新吗？（建议更新）', false);

            if ($is_udpate_force_files) {
                $need_process_files = array_merge($need_process_files, $force_update_waring_files);
            }
        }

        if (empty($need_process_files)) {
            $output->writeln('没有需要更新的文件');
            $this->cleanWorkpaceDir();

            return;
        }

        // 处理需要更新的文件

        foreach ($need_process_files as $file_path => $type) {
            $now_file_path = $now_dir . '/' . $file_path;
            $last_file_path = $last_version_dir . '/' . $file_path;

            if ($type == 'delete') {
                $output->writeln('删除文件' . $now_file_path);
                unlink($now_file_path);
            } elseif ($type == 'add') {
                $output->writeln('增加文件' . $now_file_path);
                copy($last_file_path, $now_file_path);
            } elseif ($type == 'update') {
                $output->writeln('更新文件' . $now_file_path);
                copy($last_file_path, $now_file_path);
            }
        }

        // 检测now的composer依赖和最新的composer依赖

        $last_composer_json = file_get_contents($last_version_dir . '/composer.json');

        $output->writeln($last_composer_json);
        $output->writeln('请参考以上最新composer文件调整您的依赖');

        // 分析出最新需要的但now没有的

        // 为用户整理出要手动调整的composer命令

        $output->writeln('更新完成');
        // 更新完成

        $update_tips = include $last_version_dir . '/extend/base/admin/service/adminUpdateData/tips.php';

        // 按照版本号排序
        usort($update_tips, function ($a, $b) {
            return version_compare($a['version'], $b['version']);
        });

        foreach ($update_tips as $tips_item) {
            if (version_compare($tips_item['version'], $current_version) <= 0) {
                continue;
            }

            $output->writeln('版本' . $tips_item['version'] . '更新说明:');
            $output->writeln('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

            foreach ($tips_item['desc'] as $desc) {
                $output->writeln($desc);
            }
        }

        $this->cleanWorkpaceDir();
    }

    protected function testIsOptionalFiles($file_path)
    {
        // 如果file_path以app或config开头，则是可选更新的文件
        $optional_files_prefix = [
            'app',
            'config',
            'route',
        ];

        foreach ($optional_files_prefix as $prefix) {
            if (str_starts_with($file_path, $prefix)) {
                return true;
            }
        }

        // 如果file_path不存在目录分隔符，则是可选更新的文件
        if (strpos($file_path, '/') === false) {
            return true;
        }

        return false;
    }

    protected function cleanWorkpaceDir()
    {
        $dir = App::getRuntimePath() . '/update/';

        $this->output->writeln('清理目录 ' . $dir);
        PathTools::removeDir($dir);
    }
}
