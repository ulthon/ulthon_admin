<?php

use think\migration\Migrator;
use think\migration\db\Column;

class DebugLog extends Migrator
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
        $table = $this->table('debug_log')
            ->setComment('日志表')
            ->addColumn(Column::char('uid', 30)->setDefault(''))
            ->addColumn(Column::char('level')->setDefault(''))
            ->addColumn(Column::text('content'))
            ->addColumn(Column::char('create_time_title', 30)->setDefault(''))
            ->addColumn(Column::integer('create_time')->setDefault(0))
            ->addColumn(Column::char('app_name', 30)->setDefault(''))
            ->addColumn(Column::char('controller_name', 30)->setDefault(''))
            ->addColumn(Column::char('action_name', 30)->setDefault(''))
            ->addIndex('uid')
            ->create();
    }
}
