<?php

use app\common\ColumnFormat;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableDebugMysql extends Migrator
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
    $table = $this->table('debug_log')
      ->setComment('日志表')
      ->addColumn(ColumnFormat::stringShort('uid'))
      ->addColumn(ColumnFormat::stringShort('level'))
      ->addColumn(ColumnFormat::stringLong('content'))
      ->addColumn(ColumnFormat::stringShort('app_name'))
      ->addColumn(ColumnFormat::stringShort('controller_name'))
      ->addColumn(ColumnFormat::stringShort('action_name'))
      ->addColumn(ColumnFormat::timestamp('create_time'))
      ->addColumn(ColumnFormat::stringShort('create_time_title'))
      ->create();
  }
}
