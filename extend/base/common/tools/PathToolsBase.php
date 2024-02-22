<?php

namespace base\common\tools;

use think\facade\App;

class PathToolsBase
{
    public static function publicPath($save_name)
    {
        return App::getRootPath() . 'public' . DS . $save_name;
    }

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
            try {
                mkdir($dir_name, 0777, true);
            } catch (\Exception $th) {
                if (!is_dir($dir_name)) {
                    throw $th;
                }

                return $file_path;
            }
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

    /**
     * 比较两个文件是否相同.
     *
     * @param string $a
     * @param string $b
     * @return bool 如果一致返回true，否则返回false
     */
    public static function compareFiles($a, $b):bool
    {
        if (!file_exists($a) || !file_exists($b)) {
            return false;
        }

        $result = true;

        // Check if filesize is different
        if (filesize($a) !== filesize($b)) {
            $result = false;
        }

        if ($result) {
            // Check if content is different
            $ah = fopen($a, 'rb');
            $bh = fopen($b, 'rb');

            while (!feof($ah)) {
                if (fread($ah, 8192) != fread($bh, 8192)) {
                    $result = false;
                    break;
                }
            }

            fclose($ah);
            fclose($bh);
        }

        if (!$result) {
            // 如果前面的方法认为文件变化，那么以文本变化的方式识别是否变化，并给出变化的内容
            $text_mime_type = [];
            $text_mime_type[] = 'text/html';
            $text_mime_type[] = 'text/plain';
            $text_mime_type[] = 'text/css';
            $text_mime_type[] = 'image/svg+xml';
            $text_mime_type[] = 'text/x-php';
            $text_mime_type[] = 'application/json';
            $text_mime_type[] = 'application/x-wine-extension-ini';

            if (in_array(mime_content_type($a), $text_mime_type) && in_array(mime_content_type($b), $text_mime_type)) {
                $a_content = file_get_contents($a);
                $b_content = file_get_contents($b);

                $a_content_length = strlen($a_content);
                $b_content_length = strlen($b_content);

                if ($a_content_length !== $b_content_length) {
                    $result = false;
                } else {
                    for ($i = 0; $i < $a_content_length; $i++) {
                        if ($a_content[$i] !== $b_content[$i]) {
                            $result = false;
                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }
}
