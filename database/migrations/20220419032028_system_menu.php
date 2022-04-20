<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemMenu extends Migrator
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
        $table = $this->table('system_menu')
            ->setComment('系统菜单表')
            ->addColumn(Column::bigInteger('pid')->setUnsigned()->setDefault(0)->setComment('父id'))
            ->addColumn(Column::char('title', 100)->setDefault('')->setComment('名称'))
            ->addColumn(Column::char('icon', 100)->setDefault('')->setComment('菜单图标'))
            ->addColumn(Column::char('href', 100)->setDefault('')->setComment('链接'))
            ->addColumn(Column::text('params')->setComment('链接参数'))
            ->addColumn(Column::char('target', 20)->setDefault('_self')->setComment('链接打开方式'))
            ->addColumn(Column::integer('sort')->setDefault(0)->setComment('排序'))
            ->addColumn(Column::tinyInteger('status')->setLimit(1)->setDefault(1)->setComment('状态 {radio} (0:禁用,1:启用)'))
            ->addColumn(Column::char('remark')->setDefault('')->setComment('备注说明'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setUnsigned()->setDefault(0)->setComment('创建时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addIndex('title')
            ->addIndex('href')
            ->create();
    }
}
