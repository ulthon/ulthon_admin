<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MallGoods extends Migrator
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
        $table = $this->table('mall_goods')
            ->setComment('商品列表')
            ->addColumn(Column::bigInteger('cate_id')->setUnsigned()->setComment('分类ID'))
            ->addColumn(Column::char('title', 20)->setDefault('')->setComment('商品名称'))
            ->addColumn(Column::char('tag', 100)->setDefault('')->setComment('商品标签'))
            ->addColumn(Column::char('logo')->setComment('商品logo {image}'))
            ->addColumn(Column::text('images')->setComment('商品图片 {images}'))
            ->addColumn(Column::text('describe')->setComment('商品描述 {editor}'))
            ->addColumn(Column::decimal('market_price')->setDefault(0)->setComment('市场价'))
            ->addColumn(Column::decimal('discount_price')->setDefault(0)->setComment('折扣价'))
            ->addColumn(Column::integer('sales')->setUnsigned()->setDefault(0)->setComment('销量'))
            ->addColumn(Column::integer('virtual_sales')->setUnsigned()->setDefault(0)->setComment('虚拟销量'))
            ->addColumn(Column::integer('stock')->setUnsigned()->setDefault(0)->setComment('库存'))
            ->addColumn(Column::integer('total_stock')->setUnsigned()->setDefault(0)->setComment('总库存'))
            ->addColumn(Column::integer('sort')->setUnsigned()->setDefault(0)->setComment('排序'))
            ->addColumn(Column::tinyInteger('status')->setUnsigned()->setLimit(1)->setDefault(0)->setComment('状态 {radio} (0:正常,1:禁用)'))
            ->addColumn(Column::char('remark')->setDefault('')->setComment('备注说明'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setUnsigned()->setDefault(0)->setComment('创建时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setUnsigned()->setDefault(0))
            ->addIndex('cate_id')
            ->create();
    }
}
