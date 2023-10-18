<?php

namespace think;

class UlthonAdminApp extends App
{
    /**
     * 加载应用文件和配置.
     * @return void
     */
    protected function load(): void
    {
        // 引入系统函数
        include App::getRootPath() . '/extend/base/helper.php';

        parent::load();
    }
}
