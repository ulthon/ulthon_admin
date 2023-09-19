<?php

namespace app\common\tools;

use think\facade\App;

class PathTools
{
    /**
     * 系统生成的文件,这些文件应当是可以任意删除的.
     *
     * @param string $file_name
     * @return string
     */
    public static function publicBuildPath($file_name)
    {
        $file_path = App::getRootPath() . 'public/build/' . $file_name;

        return self::intiDir($file_path);
    }

    public static function publicBuildSaveName($file_name)
    {
        return '/build/' . $file_name;
    }

    public static function safeBuildPath($save_name)
    {
        $file_path = App::getRootPath() . 'storage/' . $save_name;

        return self::intiDir($file_path);
    }

    public static function tempBuildPath($file_name)
    {
        $runtime_path = App::getRuntimePath() . 'temp/' . $file_name;

        return self::intiDir($runtime_path);
    }

    public static function intiDir($file_path, $is_dirname = false)
    {
        if (!$is_dirname) {
            $dir_name = dirname($file_path);
        } else {
            $dir_name = $file_path;
        }

        if (!is_dir($dir_name)) {
            mkdir($dir_name, 0777, true);
        }

        return $file_path;
    }

    public static function removeDir($dir_name)
    {
        if (!is_dir($dir_name)) {
            return false;
        }

        if (strpos(strtolower(PHP_OS), 'win') === 0) {
            $dir_name = static::formatWinPath($dir_name);
            exec("rd /s /q {$dir_name}", $output);

            return;
        }

        $handle = opendir($dir_name);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                is_dir("$dir_name/$file") ? self::removeDir("$dir_name/$file") : unlink("$dir_name/$file");
            }
        }
        closedir($handle);

        return rmdir($dir_name);
    }

    public static function mapDir($dir, $callback = null)
    {
        $result = [];
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, ['.', '..'])) {
                $current_path = $dir . DS . $value;
                if (is_dir($current_path)) {
                    $result[$value] = self::mapDir($current_path, $callback);
                } else {
                    if (is_callable($callback)) {
                        $result[$value] = $callback($current_path, $value, $dir);
                    } else {
                        $result[$value] = $current_path;
                    }
                }
            }
        }

        return $result;
    }

    public static function formatWinPath($content)
    {
        return str_replace('/', '\\', $content);
    }
}
