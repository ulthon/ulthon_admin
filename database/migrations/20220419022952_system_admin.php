<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemAdmin extends Migrator
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
        $table = $this->table('system_admin')
            ->setComment('系统用户表')
            ->addColumn(Column::char('auth_ids')->setDefault('')->setComment('角色权限ID'))
            ->addColumn(Column::char('head_img')->setDefault('')->setComment('头像'))
            ->addColumn(Column::char('username', 50)->setDefault('')->setUnique()->setComment('用户登录名'))
            ->addColumn(Column::char('password', 40)->setDefault('')->setComment('用户密码'))
            ->addColumn(Column::char('phone',16)->setDefault('')->setComment('联系手机号'))
            ->addColumn(Column::char('remark')->setDefault('')->setComment('备注说明'))
            ->addColumn(Column::bigInteger('login_num')->setUnsigned()->setDefault(0)->setComment('登录次数'))
            ->addColumn(Column::integer('sort')->setDefault(0)->setComment('排序'))
            ->addColumn(Column::tinyInteger('status')->setLimit(1)->setUnsigned()->setDefault(0)->setComment('状态 {radio} (0:禁用,1:启用,)'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setUnsigned()->setDefault(0)->setComment('创建时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addIndex('username')
            ->addIndex('phone')
            ->addIndex('delete_time')
            ->create();
    }
}
