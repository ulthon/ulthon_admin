<?php

use app\admin\model\SystemConfig;
use app\admin\service\InitAdminService;
use app\admin\service\TriggerService;
use think\migration\Seeder;

class InitBaseAdminData extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run():void
    {
        $output = $this->getOutput();

        TriggerService::updateSysconfig();

        $install_lock = sysconfig('base_admin_install', true, 0);

        if ($install_lock == 1) {
            $output->writeln('系统已初始化，跳过当前程序');

            return;
        }

        $model_config = SystemConfig::where('group', 'system')
            ->where('name', 'base_admin_install')
            ->find();

        if (empty($model_config)) {
            $model_config = new SystemConfig();

            $model_config->group = 'system';
            $model_config->name = 'base_admin_install';
            $model_config->remark = '系统初始化锁定，请勿修改，避免插入重复数据';
        }

        $model_config->value = 1;

        $model_config->save();

        TriggerService::updateSysconfig();

        (new InitAdminService($output))->init();
    }
}
