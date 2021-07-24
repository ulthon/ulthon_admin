<?php

namespace think\log\driver;

use app\model\DebugLog;
use think\contract\LogHandlerInterface;
use think\facade\Log;

class DebugMysql implements LogHandlerInterface
{

  public function save(array $log): bool
  {

    $create_time = time();

    $create_time_title = date('Y-m-d H:i:s', $create_time);

    foreach ($log as $log_level => $log_list) {
      foreach ($log_list as $key => $log_item) {
        DebugLog::create([
          'level' => $log_level,
          'content' => $log_item,
          'create_time' => $create_time,
          'create_time_title' => $create_time_title,
        ]);
      }
    }

    return true;
  }
}
