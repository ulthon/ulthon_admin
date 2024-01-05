<?php

namespace think\log\driver;

use PDO;
use think\contract\LogHandlerInterface;
use think\facade\App;

class DebugMysql implements LogHandlerInterface
{
    protected $enableLog = true;

    protected $config = [];

    /**
     * @var PDO
     */
    protected $pdo = null;

    protected $file = null;

    protected $fileRescource = null;

    protected $tableName = '';

    protected $reConnectTimes = 0;

    protected $fileLogTimes = 0;

    public $devMode = false;

    /**
     * 服务器断线标识字符.
     *
     * @var array
     */
    protected $breakMatchStr = [
        'server has gone away',
        'no connection to the server',
        'Lost connection',
        'is dead or not enabled',
        'Error while sending',
        'decryption failed or bad record mac',
        'server closed the connection unexpectedly',
        'SSL connection has been closed unexpectedly',
        'Error writing data to the connection',
        'Resource deadlock avoided',
        'failed with errno',
        'child connection forced to terminate due to client_idle_limit',
        'query_wait_timeout',
        'reset by peer',
        'Physical connection is not usable',
        'TCP Provider: Error code 0x68',
        'ORA-03114',
        'Packets out of order. Expected',
        'Adaptive Server connection failed',
        'Communication link failure',
        'connection is no longer usable',
        'Login timeout expired',
        'SQLSTATE[HY000] [2002] Connection refused',
        'running with the --read-only option so it cannot execute this statement',
        'The connection is broken and recovery is not possible. The connection is marked by the client driver as unrecoverable. No attempt was made to restore the connection.',
        'SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed: Try again',
        'SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo failed: Name or service not known',
        'SQLSTATE[HY000]: General error: 7 SSL SYSCALL error: EOF detected',
        'SQLSTATE[HY000] [2002] Connection timed out',
        'SSL: Connection timed out',
        'SQLSTATE[HY000]: General error: 1105 The last transaction was aborted due to Seamless Scaling. Please retry.',
    ];

    public function __construct(App $app, $config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }

        try {
            $this->initConnect();
        } catch (\Throwable $th) {
            $this->pdo = null;
            $this->initFile();
        }

        $this->tableName = $config['prefix'] . 'debug_log';
    }

    public function save(array $log): bool
    {
        $app_name = app('http')->getName() ?: '';

        $controller_name = '';
        $action_name = '';

        if (App::runningInConsole()) {
            $app_name = 'cli';
        } else {
            $controller_name = request()->controller();
            $action_name = request()->action();
        }

        $create_time = time();

        $create_time_title = date('Y-m-d H:i:s', $create_time);

        $log_key = '';

        if (defined('REUQEST_UID')) {
            $log_key = REUQEST_UID;
        } else {
            $log_key = uniqid();
        }

        foreach ($log as $log_level => $log_list) {
            foreach ($log_list as $key => $log_item) {
                if (!is_string($log_item)) {
                    $log_item = print_r($log_item, true);
                }

                $log_data = [
                    'level' => $log_level,
                    'content' => $log_item,
                    'create_time' => $create_time,
                    'create_time_title' => $create_time_title,
                    'uid' => $log_key,
                    'app_name' => $app_name,
                    'controller_name' => $controller_name,
                    'action_name' => $action_name,
                ];

                try {
                    if (!is_null($this->pdo)) {
                        $this->saveByConnect($log_data);
                    } else {
                        $this->saveByFile($log_data);
                    }
                } catch (\Throwable $th) {
                    $this->saveByFile($log_data);
                }
            }
        }

        return true;
    }

    protected function saveByConnect($log_data)
    {
        if (is_null($this->pdo)) {
            $this->saveByFile($log_data);

            return;
        }

        $this->devLog('save by connect');
        $prepare_name = [];
        foreach ($log_data as $key => $value) {
            $prepare_name[] = ':' . $key;
        }

        $data_keys = array_keys($log_data);

        $data_keys_in_sql = implode(',', $data_keys);

        $prepare_name_in_sql = implode(',', $prepare_name);

        $sql = "INSERT INTO {$this->tableName} ($data_keys_in_sql)  VALUES ($prepare_name_in_sql);";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($log_data);
        } catch (\Exception $th) {
            if ($this->isBreak($th)) {
                if ($this->reConnectTimes > 3) {
                    $this->initFile();
                    throw $th;
                }
                $this->initConnect();
                $this->reConnectTimes++;
                $this->devLog('reconnect ' . $this->reConnectTimes);
                $this->saveByConnect($log_data);
            } else {
                $this->saveByFile($log_data);
            }
        }
    }

    protected function saveByFile($log_data)
    {
        $this->devLog('save by file');

        // 如果文件日志超过100条，尝试重新通过数据库连接
        if ($this->fileLogTimes > 10) {
            $this->fileLogTimes = 0;
            $this->initConnect();
            $this->saveByConnect($log_data);

            return;
        }

        try {
            fputcsv($this->fileRescource, $log_data);
            $this->fileLogTimes++;
        } catch (\Throwable $th) {
            $this->initFile();
            $this->fileLogTimes++;
            $this->saveByFile($log_data);
        }
    }

    protected function initConnect()
    {
        $this->devLog('init connect');

        if (!is_null($this->pdo)) {
            $this->pdo = null;
        }

        $this->reConnectTimes = 0;

        $config = $this->config;

        $dsn = $this->parseDsn($config);
        try {
            $pdo = $this->createPdo($dsn, $config['username'], $config['password'], $config['params']);
            $this->pdo = $pdo;
        } catch (\Throwable $th) {
            $this->pdo = null;
        }

        return $this;
    }

    protected function initFile()
    {
        $this->devLog('init file');

        if (!is_null($this->fileRescource)) {
            return $this;
        }

        $log_path = App::getRuntimePath() . 'log/' . date('ymd') . '.csv';

        $dirname = dirname($log_path);

        if (!is_dir($dirname)) {
            mkdir($log_path, 0777, true);
        }

        $first_line = false;
        if (!file_exists($log_path)) {
            $first_line = true;
        }

        $this->fileRescource = fopen($log_path, 'a');

        if ($first_line) {
            $fields = [
                'level',
                'content',
                'create_time',
                'create_time_title',
                'uid',
                'app_name',
                'controller_name',
                'action_name',
            ];
            fputcsv($this->fileRescource, $fields);
        }

        return $this;
    }

    /**
     * 是否断线
     *
     * @param \PDOException|\Exception $e 异常对象
     *
     * @return bool
     */
    protected function isBreak($e): bool
    {
        $error = $e->getMessage();

        foreach ($this->breakMatchStr as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 解析pdo连接的dsn信息.
     * @param  array $config 连接信息
     * @return string
     */
    protected function parseDsn(array $config): string
    {
        if (!empty($config['socket'])) {
            $dsn = 'mysql:unix_socket=' . $config['socket'];
        } elseif (!empty($config['hostport'])) {
            $dsn = 'mysql:host=' . $config['hostname'] . ';port=' . $config['hostport'];
        } else {
            $dsn = 'mysql:host=' . $config['hostname'];
        }
        $dsn .= ';dbname=' . $config['database'];

        if (!empty($config['charset'])) {
            $dsn .= ';charset=' . $config['charset'];
        }

        return $dsn;
    }

    protected function createPdo($dsn, $username, $password, $params)
    {
        return new PDO($dsn, $username, $password, $params);
    }

    public function __destruct()
    {
        $this->pdo = null;

        if (!is_null($this->fileRescource)) {
            fclose($this->fileRescource);
        }
    }

    protected function devLog($content)
    {
        if ($this->devMode) {
            dump($content);
        }
    }
}
