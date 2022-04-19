<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemAuthNode extends Migrator
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
        $table = $this->table('system_auth_node')
            ->setComment('角色与节点关系表')
            ->addColumn(Column::bigInteger('auth_id')->setUnsigned()->setComment('角色ID'))
            ->addColumn(Column::bigInteger('node_id')->setUnsigned()->setComment('节点ID'))
            ->addIndex('auth_id')
            ->addIndex('node_id')
            ->create();
    }
}
