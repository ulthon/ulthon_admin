<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableUser extends Migrator
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
        $table = $this->table('user',['comment'=>'用户表','signed'=>false]);
        $table->addColumn('account','string',['limit'=>20,'comment'=>'用户帐号']);
        $table->addColumn('password','string',['limit'=>32,'comment'=>'密码']);
        $table->addColumn('salt','string',['limit'=>6,'comment'=>'密码盐']);
        $table->addColumn('nickname','string',['limit'=>10,'comment'=>'昵称']);
        $table->addColumn('avatar','string',['limit'=>40,'comment'=>'头像地址']);
        $table->addColumn('create_time','integer',['limit'=>10,'default'=>0,'comment'=>'添加时间']);
        $table->addColumn('update_time','integer',['limit'=>10,'default'=>0,'comment'=>'更新时间']);
        $table->addColumn('delete_time','integer',['limit'=>10,'default'=>0,'comment'=>'删除时间']);
        $table->addColumn('last_login_time','integer',['limit'=>10,'default'=>0,'comment'=>'最后一次登陆时间']);
        $table->addColumn('status','integer',['limit'=>1,'default'=>0,'comment'=>'状态']);
        $table->addIndex('account');
        $table->addIndex('delete_time');
        $table->create();
    }
}
