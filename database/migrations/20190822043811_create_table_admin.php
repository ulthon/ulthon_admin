<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableAdmin extends Migrator
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
        $table = $this->table('admin',['comment'=>'管理员表','signed'=>false]);
        $table->addColumn('account','string',['limit'=>20,'comment'=>'用户帐号']);
        $table->addColumn('password','string',['limit'=>32,'comment'=>'密码']);
        $table->addColumn('salt','string',['limit'=>6,'comment'=>'密码盐']);
        $table->addColumn('nickname','string',['limit'=>10,'default'=>"admin",'comment'=>'昵称']);
        $table->addColumn('avatar','string',['limit'=>40,'comment'=>'头像地址']);
        $table->addColumn('create_time','integer',['limit'=>10,'default'=>0,'comment'=>'添加时间']);
        $table->addColumn('delete_time','integer',['limit'=>10,'default'=>0,'comment'=>'删除时间']);
        $table->addColumn('group_id','integer',['limit'=>10,'default'=>0,'comment'=>'管理员组']);
        $table->addIndex('account');
        $table->addIndex('delete_time');
        $table->create();
    }
}
