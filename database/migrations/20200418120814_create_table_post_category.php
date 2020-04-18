<?php

use app\common\ColumnFormat;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateTablePostCategory extends Migrator
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
        $table = $this->table('post_category',['comment'=>'文章分类关联表']);
        $table->addColumn(ColumnFormat::integer('post_id'))
        ->addColumn(ColumnFormat::integer('category_id'))
        ->addColumn(ColumnFormat::timestamp('create_time'))
        ->addColumn(ColumnFormat::timestamp('update_time'))
        ->addColumn(ColumnFormat::timestamp('delete_time'))
        ->addIndex('post_id')
        ->addIndex('category_id')
        ->create();
        
    }
}
