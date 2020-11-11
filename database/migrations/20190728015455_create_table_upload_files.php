<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableUploadFiles extends Migrator
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
        $table = $this->table('upload_files',['comment'=>'上传的文件','signed'=>false]);
        $table->addColumn('save_name','string',['limit'=>100,'comment'=>'文件存储地址']);
        $table->addColumn('file_name','string',['limit'=>100,'comment'=>'文件原始名称']);
        $table->addColumn('mime_type','string',['limit'=>30,'comment'=>'mime type 类型']);
        $table->addColumn('file_size','integer',['limit'=>30,'comment'=>'文件大小']);
        $table->addColumn('ext_name','string',['limit'=>10,'comment'=>'扩展名']);
        $table->addColumn('file_md5','string',['limit'=>32,'comment'=>'文件md5值']);
        $table->addColumn('file_sha1','string',['limit'=>40,'comment'=>'文件sha1值']);
        $table->addColumn('create_time','integer',['limit'=>10,'default'=>0,'comment'=>'上传时间']);
        $table->addColumn('used_time','integer',['limit'=>10,'default'=>0,'comment'=>'标记使用时间，如果为空，可能仅作为了预览图']);
        $table->addColumn('delete_time','integer',['limit'=>10,'default'=>0,'comment'=>'删除时间']);
        $table->addColumn('clear_time','integer',['limit'=>10,'default'=>0,'comment'=>'清空时间']);
        $table->addColumn('type','string',['limit'=>20,'default'=>1,'comment'=>'文件类型，1：系统logo;2:管理员头像']);
        $table->addColumn('status','integer',['limit'=>2,'default'=>0,'comment'=>'文件状态:0,上传未使用,1:已使用,2:已删除,3已清除']);
        $table->addIndex('save_name');
        $table->addIndex('create_time');
        $table->addIndex('used_time');
        $table->addIndex('delete_time');
        $table->addIndex('clear_time');
        $table->addIndex('type');
        $table->addIndex('status');
        $table->create();
        
        
        
    }
}
