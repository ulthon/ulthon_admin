<?php

// 递归处理多级目录extend\base\admin，将所有以 Class.php 结尾的文件重命名为 Base.php

$dir = './extend/base/admin';

// 递归获取所有的文件
function scan_dir($dir)
{
    $files = array_diff(scandir($dir), ['..', '.']);
    foreach ($files as $file) {
        if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
            scan_dir($dir . DIRECTORY_SEPARATOR . $file);
        } else {
            if (str_ends_with($file, 'Class.php')) {
                $file_path = $dir . DIRECTORY_SEPARATOR . $file;
                $new_path = str_replace('Class.php', 'Base.php', $file_path);
                rename($file_path, $new_path);
            }
        }
    }
}
scan_dir($dir);