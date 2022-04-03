<?php

declare(strict_types=1);

namespace app\common\command;

use app\common\tools\PathTools;
use PDO;
use think\Config as ThinkConfig;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;
use think\facade\Config;
use think\facade\Db;

class Install extends Command
{

    protected $installLockPath = null;

    protected function configure()
    {
        // 指令配置
        $this->setName('install')
            ->addOption('adminname', 'u', Option::VALUE_OPTIONAL, '管理员账号')
            ->addOption('password', 'p', Option::VALUE_OPTIONAL, '管理员密码')
            ->addOption('force', 'f', Option::VALUE_OPTIONAL, '强制安装')
            ->setDescription('安装数据库');

        $this->installLockPath = App::getRootPath() . '/config/install/lock/install.lock';
    }



    protected function execute(Input $input, Output $output)
    {


        // 指令输出

        $force = $input->getOption('force');

        if (is_null($force)) {
            $install_lock_path = $this->installLockPath;

            if (is_file($install_lock_path)) {
                $errorInfo = '已安装系统，如需重新安装,可以添加 -f 1 参数或删除文件：/config/install/lock/install.lock';
                $output->writeln($errorInfo);
                return false;
            }
        }

        if (!$this->checkConnect()) {
            $output->writeln('数据库连接失败,请检查数据库配置');
        }

        $adminname = $input->getOption('adminname') ?: 'admin';
        $password = $input->getOption('password') ?: 'admin';

        $install_result = $this->install($adminname, $password);

        if ($install_result !== true) {
            $output->writeln($install_result);
            return false;
        }
    }

    protected function install($username, $password)
    {
        $install_lock_path = $this->installLockPath;

        $sql_path = App::getRootPath() . '/config/install/sql/install.sql';

        $sql_content = file_get_contents($sql_path);

        $sqlArray = $this->parseSql($sql_content);

        Db::startTrans();
        try {
            foreach ($sqlArray as $vo) {
                if (strpos($vo, 'LOCK TABLES') === 0) {
                    continue;
                }
                if (strpos($vo, 'UNLOCK') === 0) {
                    continue;
                }
                Db::execute($vo);
            }
            Db::name('system_admin')
                ->where('id', 1)
                ->delete();
            Db::name('system_admin')
                ->insert([
                    'id'          => 1,
                    'username'    => $username,
                    'head_img'    => '/static/admin/images/head.jpg',
                    'password'    => password($password),
                    'create_time' => time(),
                ]);

            // 处理安装文件
            PathTools::intiDir($install_lock_path);
            @file_put_contents($install_lock_path, date('Y-m-d H:i:s'));
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return true;
    }

    public function checkConnect()
    {
        try {
            Db::query("select version()");
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function parseSql($sql = '')
    {
        list($pure_sql, $comment) = [[], false];
        $sql = explode("\n", trim(str_replace(["\r\n", "\r"], "\n", $sql)));

        $prefix = Config::get('database.connections.' . Config::get('database.default') . '.prefix');

        foreach ($sql as $key => $line) {
            if ($line == '') {
                continue;
            }
            if (preg_match("/^(#|--)/", $line)) {
                continue;
            }
            if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                continue;
            }
            if (substr($line, 0, 2) == '/*') {
                $comment = true;
                continue;
            }
            if (substr($line, -2) == '*/') {
                $comment = false;
                continue;
            }
            if ($comment) {
                continue;
            }



            $line = str_replace('`ul_', '`' . $prefix, $line);


            if ($line == 'BEGIN;' || $line == 'COMMIT;') {
                continue;
            }
            array_push($pure_sql, $line);
        }
        //$pure_sql = implode($pure_sql, "\n");
        $pure_sql = implode("\n", $pure_sql);
        $pure_sql = explode(";\n", $pure_sql);
        return $pure_sql;
    }
}
