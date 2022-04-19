<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MallCate extends Migrator
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
        $table = $this->table('mall_cate')
            ->setComment('商品分类')
            ->addColumn(Column::char('title', 20)->setDefault('')->setComment('分类名'))
            ->addColumn(Column::char('image')->setDefault('')->setComment('分类图片 {image}'))
            ->addColumn(Column::integer('sort')->setDefault(0)->setComment('排序'))
            ->addColumn(Column::tinyInteger('status')->setLimit(1)->setDefault(2)->setComment('状态 {radio} (1:禁用,2:启用)'))
            ->addColumn(Column::char('remark')->setDefault('')->setComment('备注说明'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addIndex('title')
            ->save();
    }
}
