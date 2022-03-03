<?php

// +----------------------------------------------------------------------
// | EasyAdmin
// +----------------------------------------------------------------------
// | PHP交流群: 763822524
// +----------------------------------------------------------------------
// | 开源协议  https://mit-license.org 
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zhongshaofa/EasyAdmin
// +----------------------------------------------------------------------

namespace app\common\command;


use app\admin\model\SystemNode;
use EasyAdmin\console\CliEcho;
use app\common\tools\BuildCurd;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use EasyAdmin\auth\Node as NodeService;
use think\Exception;

class Curd extends Command
{
    protected function configure()
    {
        $this->setName('curd')
            ->addOption('table', 't', Option::VALUE_REQUIRED, '主表名', null)
            ->addOption('controllerFilename', 'c', Option::VALUE_REQUIRED, '控制器文件名', null)
            ->addOption('modelFilename', 'm', Option::VALUE_REQUIRED, '主表模型文件名', null)
            #
            ->addOption('force', 'f', Option::VALUE_REQUIRED, '强制覆盖模式', 0)
            ->addOption('delete', 'd', Option::VALUE_REQUIRED, '删除模式', 0)
            ->setDescription('一键curd命令服务');
    }

    protected function execute(Input $input, Output $output)
    {

        $table = $input->getOption('table');
        $controllerFilename = $input->getOption('controllerFilename');
        $modelFilename = $input->getOption('modelFilename');

        $force = $input->getOption('force');
        $delete = $input->getOption('delete');



        if (empty($table)) {
            CliEcho::error('请设置主表');
            return false;
        }

        try {
            $build = (new BuildCurd())
                ->setTable($table)
                ->setForce($force);

            $columns = $build->getTableColumns();


            $relations = [];


            foreach ($columns as $field => $column) {

                if (isset($column['formType']) && $column['formType'] == 'relation') {
                    $define = $column['define'];

                    if (!isset($define['table'])) {
                        CliEcho::error("关联字段{$field}没有设置关联表名称");
                        return false;
                    }

                    $relations[] = [
                        'table'         => $define['table'],
                        'foreignKey'    => $field,
                        'primaryKey'    => $define['primaryKey'] ?? null,
                        'modelFilename' => $define['modelFilename'] ?? null,
                        'onlyFileds'    => isset($define['onlyFileds']) ? explode("|", $define['onlyFileds']) : [],
                        'relationBindSelect' => $define['relationBindSelect'] ?? null,
                    ];
                }
            }


            !empty($controllerFilename) && $build = $build->setControllerFilename($controllerFilename);
            !empty($modelFilename) && $build = $build->setModelFilename($modelFilename);

            
            foreach ($relations as $relation) {
                $build = $build->setRelation($relation['table'], $relation['foreignKey'], $relation['primaryKey'], $relation['modelFilename'], $relation['onlyFileds'], $relation['relationBindSelect']);
            }

            $build = $build->render();
            $fileList = $build->getFileList();

            if (!$delete) {
                if ($force) {
                    $output->info(">>>>>>>>>>>>>>>");
                    foreach ($fileList as $key => $val) {
                        $output->info($key);
                    }
                    $output->info(">>>>>>>>>>>>>>>");
                    $output->info("确定强制生成上方所有文件? 如果文件存在会直接覆盖。 请输入 'yes' 按回车键继续操作: ");
                    $line = fgets(defined('STDIN') ? STDIN : fopen('php://stdin', 'r'));
                    if (trim($line) != 'yes') {
                        throw new Exception("取消文件CURD生成操作");
                    }
                }
                $result = $build->create();
                CliEcho::success('自动生成CURD成功');
            } else {
                $output->info(">>>>>>>>>>>>>>>");
                foreach ($fileList as $key => $val) {
                    $output->info($key);
                }
                $output->info(">>>>>>>>>>>>>>>");
                $output->info("确定删除上方所有文件?  请输入 'yes' 按回车键继续操作: ");
                $line = fgets(defined('STDIN') ? STDIN : fopen('php://stdin', 'r'));
                if (trim($line) != 'yes') {
                    throw new Exception("取消删除文件操作");
                }
                $result = $build->delete();
                CliEcho::success('>>>>>>>>>>>>>>>');
                CliEcho::success('删除自动生成CURD文件成功');
            }
            CliEcho::success('>>>>>>>>>>>>>>>');
            foreach ($result as $vo) {
                CliEcho::success($vo);
            }
        } catch (\Exception $e) {
            CliEcho::error($e->getMessage());
            return false;
        }
    }
}
