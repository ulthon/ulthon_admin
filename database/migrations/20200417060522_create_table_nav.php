<?php

use app\admin\controller\Common;
use app\common\ColumnFormat;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableNav extends Migrator
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
        $table = $this->table('nav',['comment'=>'多功能导航,可兼容多种类型','sign'=>false]);

        $table->addColumn('title','string',['limit'=>100,'comment'=>'名称标题']);
        $table->addColumn(Column::make('sort','integer')->setSigned(false)->setComment('排序,越小越靠前'));
        $table->addColumn(Column::make('create_time','integer')->setSigned(false)->setLimit(10)->setComment('添加时间'));
        $table->addColumn(ColumnFormat::timestamp('update_time'));
        $table->addColumn(ColumnFormat::timestamp('delete_time'));
        $table->addColumn(ColumnFormat::stringShort('type')->setComment('类型,用于区分业务场景:1:PC导航,2:PC轮播图,3:PC友情链接'));
        $table->addColumn(Column::make('img','string')->setLimit(100)->setComment('图片'));
        $table->addColumn(ColumnFormat::stringLong('desc')->setComment('副标题描述'));
        $table->addColumn(Column::make('target','string')->setLimit(10)->setSigned(false)->setComment('网页链接打开对象,_BLANK,_SELF,iframe_name'));
        $table->addColumn(Column::make('xcx_type','integer')->setLimit(10)->setComment('小程序打开方式,1:小程序导航页面,2:普通页面,3:web-view,4:其他小程序,5:电话'));
        $table->addColumn(Column::make('value','string')->setLimit(100)->setComment('对象值,有可能是网页链接,小程序导航页面路径,小程序普通页面路径,电话'));
        $table->addColumn(Column::make('xcx_appid','string')->setLimit(30)->setComment('小程序appid,目标是其他小程序是有效'));
        $table->addColumn(ColumnFormat::integerTypeStatus('status')->setComment('0:下架,1:显示'));
        $table->addColumn(ColumnFormat::stringTypeStatus('key')->setComment('英文标记'));
        $table->addIndex('delete_time');
        $table->addIndex('type');
        $table->addIndex('sort');
        $table->addIndex('status');
        $table->create();
    }
}
