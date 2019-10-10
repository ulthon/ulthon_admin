<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableAdminGroup extends Migrator
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
        $table = $this->table('admin_group',[
            'comment'=>'管理员组',
            'signed'=>false
        ]);

        $table->addColumn('name','string',['limit'=>20,'comment'=>'组名']);
        $table->addColumn('create_time','integer',['limit'=>11,'default'=>0,'comment'=>'添加时间']);
        $table->addColumn('update_time','integer',['limit'=>11,'default'=>0,'comment'=>'更新时间']);
        $table->addColumn('delete_time','integer',['limit'=>11,'default'=>0,'comment'=>'删除时间']);
        $table->addColumn('permissions','text',['comment'=>'拥有权限']);
        $table->create();
    }
}
