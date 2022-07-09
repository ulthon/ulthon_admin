<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MallTag extends Migrator
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
        $table = $this->table('mall_tag')
            ->setComment('商品标签')
            ->addColumn(Column::char('title', 20)->setDefault('')->setComment('商品名称'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setUnsigned()->setDefault(0)->setComment('创建时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->create();
    }
}
