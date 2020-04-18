<?php

use app\common\ColumnFormat;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableCategory extends Migrator
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
        $table = $this->table('category')
        ->setComment('分类表')
        ->addColumn(ColumnFormat::stringNormal('title'))
        ->addColumn(ColumnFormat::timestamp('create_time'))
        ->addColumn(ColumnFormat::timestamp('update_time'))
        ->addColumn(ColumnFormat::timestamp('delete_time'))
        ->addColumn(ColumnFormat::integer('pid')->setComment('上级id'))
        ->addColumn(ColumnFormat::integer('level')->setDefault(1)->setComment('层级'))
        ->addColumn(ColumnFormat::stringShort('tpl_name')->setComment('模板名称'))
        ->addColumn(ColumnFormat::stringUrl('title_img')->setComment('附图'))
        ->addColumn(ColumnFormat::stringLong('desc')->setComment('副标题描述'))
        ->addIndex('pid')
        ->create();
    }
}
