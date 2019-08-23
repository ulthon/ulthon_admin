<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableSystemConfig extends Migrator
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
        $table = $this->table('system_config',['comment'=>'系统配置表']);
        $table->addColumn('name','string',['limit'=>30,'comment'=>'配置名称']);
        $table->addColumn('value','string',['limit'=>500,'comment'=>'值']);
        $table->addIndex('name');
        $table->save();
    }
}
