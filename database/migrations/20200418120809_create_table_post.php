<?php

use app\common\ColumnFormat;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateTablePost extends Migrator
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
        $table = $this->table('post',['comment'=>"内容文章"]);
        $table->addColumn(ColumnFormat::stringNormal('title')->setComment('标题'));
        $table->addColumn(ColumnFormat::stringUrl('poster')->setComment('封面'));
        $table->addColumn(ColumnFormat::timestamp('create_time'));
        $table->addColumn(ColumnFormat::timestamp('update_time'));
        $table->addColumn(ColumnFormat::timestamp('delete_time'));
        $table->addColumn(Column::make('content','text'));
        $table->addColumn(Column::make('content_html','text'));
        $table->addColumn(ColumnFormat::stringLong('desc')->setDefault('描述'));
        $table->addColumn(ColumnFormat::integerTypeStatus('is_top')->setComment('是否置顶'));
        $table->addColumn(ColumnFormat::integerTypeStatus('status')->setComment('1:显示,0:不显示'));
        $table->addColumn(ColumnFormat::timestamp('publish_time')->setComment('发表时间'));
        $table->addColumn(ColumnFormat::integer('hits')->setComment('访问量'));
        $table->addColumn(ColumnFormat::stringUrl('jump_to_url')->setComment('跳转链接'));
        $table->addColumn(ColumnFormat::integerTypeStatus('jump_to_url_status')->setComment('0:不显示,1:显示连接,2:自动跳转'));
        $table->addColumn(ColumnFormat::integer('sort')->setComment('排序,越大越靠前'));
        $table->addColumn(ColumnFormat::stringShort('type')->setComment('类型,1:文章,有分类有标签,2:页面,无分类无标签,N:其他形式用,用于区分不同的用途'));
        $table->addColumn(Column::make('files','text')->setComment('附件'));
        $table->addColumn(Column::make('pictures','text')->setComment('相册'));
        $table->addColumn(ColumnFormat::stringShort('tpl_name')->setComment('模板名称'));
        $table->addIndex('type');
        $table->addIndex('status');
        $table->addIndex('delete_time');
        $table->addIndex('hits');
        $table->addIndex('is_top');
        $table->create();
        

    }
}
