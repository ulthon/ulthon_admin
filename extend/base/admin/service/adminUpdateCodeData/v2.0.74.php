<?php

use think\console\Input;
use think\console\Output;
use think\facade\App;

class UpdateFunction
{
    /**
     * @var Input
     */
    public $input;

    /**
     * @var Output
     */
    public $output;

    public $replaceMap = [
        'table_elem' => 'tableElem',
        'table_render_id' => 'tableRenderId',
        'index_url' => 'indexUrl',
        'add_url' => 'addUrl',
        'edit_url' => 'editUrl',
        'delete_url' => 'deleteUrl',
        'export_url' => 'exportUrl',
        'modify_url' => 'modifyUrl',
    ];

    public function update()
    {
        $this->output->writeln('更新代码');

        $this->output->info('当前版本需要将js代码的init的属性从蛇形改为小驼峰');
        $this->output->info('您可以通过编辑器的全局搜索替换功能完成');
        $this->output->info('也可以使用当前命令替换');

        $is_true = $this->output->confirm($this->input, '是否执行替换？');

        if (!$is_true) {
            $this->output->writeln('取消更新');

            return;
        }

        // 扫描app下的所有js文件
        $js_file_list = $this->scanJsFile();

        // 将文件的内容替换
        foreach ($js_file_list as $file_path) {
            $file_content = file_get_contents($file_path);

            foreach ($this->replaceMap as $search => $replace) {
                $file_content = str_replace($search, $replace, $file_content);
            }

            file_put_contents($file_path, $file_content);
        }

        $this->output->writeln('更新代码完成');
        $this->output->writeln('请注意查看您的代码变动');
    }

    /**
     * 扫描js文件.
     *
     * @return array
     */
    public function scanJsFile()
    {
        $js_file_list = [];

        $app_path = App::getRootPath() . '/app';

        $this->scanDir($app_path, $js_file_list);

        return $js_file_list;
    }

    /**
     * 扫描目录.
     *
     * @param string $dir
     * @param array $js_file_list
     * @return void
     */
    public function scanDir($dir, &$js_file_list)
    {
        $dir_list = scandir($dir);

        foreach ($dir_list as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $file_path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($file_path)) {
                $this->scanDir($file_path, $js_file_list);
            } else {
                $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);

                if ($file_ext == 'js') {
                    $js_file_list[] = $file_path;
                }
            }
        }
    }
}
