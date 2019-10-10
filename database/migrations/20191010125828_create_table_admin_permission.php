<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableAdminPermission extends Migrator
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
        $table = $this->table('admin_permission',[
            'comment'=>'后台权限记录',
            'signed'=>false
        ]);

        $table->addColumn('name','string',['limit'=>20,'default'=>'0','comment'=>'权限名称']);
        $table->addColumn('app','string',['limit'=>50,'comment'=>'应用名']);
        $table->addColumn('controller','string',['limit'=>50,'comment'=>'控制器名']);
        $table->addColumn('action','string',['limit'=>50,'comment'=>'方法名']);
        $table->addColumn('is_log','integer',['limit'=>1,'default'=>0,'comment'=>'是否把这个访问记录下来']);
        $table->addIndex('app');
        $table->addIndex('controller');
        $table->addIndex('action');
        $table->addIndex('is_log');
        $table->create();
    }
}
