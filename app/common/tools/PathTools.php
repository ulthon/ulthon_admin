<?php

namespace app\common\tools;

use Diff;
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

    public static function compareFiles($a, $b)
    {
        $text_mime_type = [];
        $text_mime_type[] = 'text/html';
        $text_mime_type[] = 'text/plain';
        $text_mime_type[] = 'text/css';
        $text_mime_type[] = 'image/svg+xml';
        $text_mime_type[] = 'text/x-php';
        $text_mime_type[] = 'application/json';
        $text_mime_type[] = 'application/x-wine-extension-ini';

        if (in_array(mime_content_type($a), $text_mime_type) && in_array(mime_content_type($b), $text_mime_type)) {
            // 如果都是文本文件，则执行内容对比
            $diff = new Diff(file_get_contents($a), file_get_contents($b));
            $diff_content = $diff->getGroupedOpcodes();

            if (!empty($diff_content)) {
                return false;
            } else {
                return true;
            }
        }

        // Check if filesize is different
        if (filesize($a) !== filesize($b)) {
            return false;
        }

        // Check if content is different
        $ah = fopen($a, 'rb');
        $bh = fopen($b, 'rb');

        $result = true;
        while (!feof($ah)) {
            if (fread($ah, 8192) != fread($bh, 8192)) {
                $result = false;
                break;
            }
        }

        fclose($ah);
        fclose($bh);

        return $result;
    }
}
