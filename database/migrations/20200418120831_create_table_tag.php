<?php

use app\common\ColumnFormat;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableTag extends Migrator
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
        $table = $this->table('tag')
            ->setComment('分类表')
            ->addColumn(ColumnFormat::stringNormal('title'))
            ->addColumn(ColumnFormat::timestamp('create_time'))
            ->addColumn(ColumnFormat::timestamp('update_time'))
            ->addColumn(ColumnFormat::timestamp('delete_time'))
            ->addColumn(ColumnFormat::stringShort('type')->setComment('类型,1:文章,有分类有标签,2:页面,无分类无标签,N:其他形式用,用于区分不同的用途'))
            ->addIndex('type')
            ->create();
    }
}
