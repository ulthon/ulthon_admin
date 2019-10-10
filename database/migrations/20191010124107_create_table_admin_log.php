<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableAdminLog extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('admin_log',[
            'comment'=>'管理员日志',
            'signed'=>false
        ]);

        $table->addColumn('app','string',['limit'=>50,'comment'=>'应用名']);
        $table->addColumn('controller','string',['limit'=>50,'comment'=>'控制器名']);
        $table->addColumn('action','string',['limit'=>50,'comment'=>'方法名']);
        $table->addColumn('param','text',['comment'=>'参数']);
        $table->addColumn('create_time','integer',['limit'=>11,'default'=>0,'comment'=>'添加时间']);
        $table->addColumn('delete_time','integer',['limit'=>11,'default'=>0,'comment'=>'删除时间']);
        $table->addColumn('admin_id','integer',['limit'=>20,'default'=>0,'comment'=>'管理员id']);
        $table->addColumn('ip','string',['limit'=>30,'default'=>'','comment'=>'客户端ip']);
        $table->addIndex('app');
        $table->addIndex('controller');
        $table->addIndex('action');
        $table->addIndex('delete_time');
        $table->addIndex('admin_id');
        $table->create();


    }
}
