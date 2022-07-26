<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemUploadfile extends Migrator
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
        $table = $this->table('system_uploadfile')
            ->setComment('上传文件表')
            ->addColumn(Column::char('upload_type', 20)->setDefault('local')->setComment('存储位置'))
            ->addColumn(Column::char('original_name')->setDefault('')->setComment('文件原名'))
            ->addColumn(Column::char('url')->setDefault('')->setComment('链接'))
            ->addColumn(Column::char('save_name')->setDefault('')->setComment('存储名称'))
            ->addColumn(Column::char('image_width', 30)->setDefault('')->setComment('宽度'))
            ->addColumn(Column::char('image_height', 30)->setDefault('')->setComment('高度'))
            ->addColumn(Column::integer('image_frames', 10)->setUnsigned()->setDefault(0)->setComment('图片帧数'))
            ->addColumn(Column::char('mime_type', 100)->setDefault('')->setComment('mime类型'))
            ->addColumn(Column::integer('file_size')->setUnsigned()->setDefault(0)->setComment('文件大小'))
            ->addColumn(Column::char('file_ext', 100)->setDefault('')->setComment('扩展名'))
            ->addColumn(Column::char('sha1', 40)->setDefault('')->setComment('文件 sha1编码'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setUnsigned()->setDefault(0)->setComment('创建时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addIndex('upload_type')
            ->addIndex('original_name')
            ->addIndex('delete_time')
            ->create();
    }
}
