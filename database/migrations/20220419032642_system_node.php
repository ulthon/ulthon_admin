<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemNode extends Migrator
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
        $table = $this->table('system_node')
            ->setComment('系统节点表')
            ->addColumn(Column::char('node', 100)->setDefault('')->setComment('节点代码'))
            ->addColumn(Column::char('title')->setDefault('')->setComment('节点标题'))
            ->addColumn(Column::tinyInteger('type')->setUnsigned()->setLimit(1)->setComment('节点类型（1：控制器，2：节点）'))
            ->addColumn(Column::tinyInteger('is_auth')->setUnsigned()->setLimit(1)->setDefault(1)->setComment('是否启动RBAC权限控制'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addIndex('node')
            ->create();
    }
}
