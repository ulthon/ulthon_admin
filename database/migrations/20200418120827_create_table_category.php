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
            ->addColumn(ColumnFormat::integer('sort')->setComment('排序:越小越靠前'))
            ->addColumn(ColumnFormat::integer('level')->setDefault(1)->setComment('层级'))
            ->addColumn(ColumnFormat::stringShort('tpl_name')->setComment('模板名称'))
            ->addColumn(ColumnFormat::stringUrl('title_img')->setComment('附图'))
            ->addColumn(ColumnFormat::stringLong('desc')->setComment('副标题描述'))
            ->addColumn(ColumnFormat::integerTypeStatus('status')->setComment('0:不显示,1:显示'))
            ->addColumn(ColumnFormat::stringShort('type')->setComment('类型,1:文章,有分类有标签,2:页面,无分类无标签,N:其他形式用,用于区分不同的用途'))
            ->addIndex('type')
            ->addIndex('pid')
            ->addIndex('status')
            ->addIndex('delete_time')
            ->create();
    }
}
