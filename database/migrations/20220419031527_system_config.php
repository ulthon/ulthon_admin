<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemConfig extends Migrator
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
        $table = $this->table('system_config')
            ->setComment('系统配置表')
            ->addColumn(Column::char('name', 30)->setDefault('')->setComment('变量名'))
            ->addColumn(Column::char('group', 30)->setDefault('')->setComment('分组'))
            ->addColumn(Column::text('value')->setDefault('')->setComment('变量值'))
            ->addColumn(Column::char('remark', 100)->setDefault('')->setComment('备注信息'))
            ->addColumn(Column::integer('sort')->setDefault(0)->setComment('排序'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setUnsigned()->setDefault(0)->setComment('创建时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addIndex('name')
            ->addIndex('group')
            ->create();
    }
}
