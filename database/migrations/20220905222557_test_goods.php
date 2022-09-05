<?php

use think\migration\Migrator;
use think\migration\db\Column;

class TestGoods extends Migrator
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
        $table = $this->table('test_goods')
            ->setComment('商品列表')
            ->addColumn('cate_id', 'biginteger', ['limit' => '20', 'signed' => '0', 'null' => '0', 'default' => '0', 'comment' => '分类ID {relation} (table:mall_cate,relationBindSelect:title)',])
            ->addColumn('title', 'char', ['limit' => '20', 'null' => '0', 'default' => '', 'comment' => '商品名称',])
            ->addColumn('logo', 'char', ['limit' => '255', 'null' => '0', 'comment' => '商品logo {image}',])
            ->addColumn('images', 'text', ['null' => '0', 'comment' => '商品图片 {images}',])
            ->addColumn('describe', 'text', ['null' => '0', 'comment' => '商品描述 {editor}',])
            ->addColumn('total_stock', 'integer', ['limit' => '11', 'signed' => '0', 'null' => '0', 'default' => '0', 'comment' => '总库存',])
            ->addColumn('sort', 'integer', ['limit' => '11', 'signed' => '0', 'null' => '0', 'default' => '0', 'comment' => '排序',])
            ->addColumn('status', 'integer', ['limit' => '1', 'signed' => '0', 'null' => '0', 'default' => '0', 'comment' => '状态 {radio} (0:正常,1:禁用)',])
            ->addColumn('cert_file', 'string', ['limit' => '100', 'null' => '0', 'comment' => '合格证 {file}',])
            ->addColumn('verfiy_file', 'text', ['null' => '0', 'comment' => '检测报告 {files}',])
            ->addColumn('remark', 'char', ['limit' => '255', 'null' => '0', 'default' => '', 'comment' => '备注说明',])
            ->addColumn('create_time', 'integer', ['limit' => '11', 'signed' => '0', 'null' => '0', 'default' => '0', 'comment' => '',])
            ->addColumn('update_time', 'integer', ['limit' => '11', 'signed' => '0', 'null' => '0', 'default' => '0', 'comment' => '',])
            ->addColumn('delete_time', 'integer', ['limit' => '11', 'signed' => '0', 'null' => '0', 'default' => '0', 'comment' => '',])
            ->addColumn('publish_time', 'integer', ['limit' => '10', 'signed' => '0', 'null' => '0', 'comment' => '发布日期 {date} (date)',])
            ->addColumn('sale_time', 'biginteger', ['limit' => '20', 'signed' => '0', 'null' => '0', 'comment' => '售卖日期 {date} (datetime)',])
            ->addColumn('intro', 'string', ['limit' => '100', 'null' => '0', 'comment' => '简介 {textarea}',])
            ->addColumn('time_status', 'integer', ['limit' => '5', 'signed' => '0', 'null' => '0', 'comment' => '秒杀状态 {select} (0:未参加,1:已开始,3:已结束)',])
            ->addColumn('is_recommend', 'integer', ['limit' => '4', 'null' => '0', 'comment' => '是否推荐 {switch} (0:不推荐,1:推荐)',])
            ->addColumn('shop_type', 'string', ['limit' => '100', 'null' => '0', 'comment' => '商品类型 {checkbox} (taobao:淘宝,jd:京东)',])
            ->addColumn('tag', 'string', ['limit' => '100', 'null' => '0', 'comment' => '商品标签 {table} (table:mall_tag,type:checkbox,valueField:id,fieldName:title)',])
            ->addColumn('tag_backup', 'string', ['limit' => '100', 'null' => '1', 'comment' => '商品标签（单选） {table} (table:mall_tag,type:radio,valueField:id,fieldName:title)',])
            ->addColumn('from_area', 'string', ['limit' => '100', 'null' => '0', 'comment' => '产地 {city} (name-province:0,code:0)',])
            ->addColumn('store_city', 'string', ['limit' => '100', 'null' => '0', 'default' => '山东省/临沂市', 'comment' => '仓库 {city} (level:city)',])
            ->addColumn('tag_input', 'string', ['limit' => '100', 'null' => '0', 'comment' => '商品标签 （输入） {tag}',])
            ->addColumn('uid', 'string', ['limit' => '100', 'null' => '0', 'comment' => '唯一id',])
            ->addColumn('price', 'decimal', ['limit' => '10,2', 'null' => '1', 'comment' => '价格',])
            ->addColumn('detail', 'text', ['null' => '1', 'comment' => '详情',])
            ->addIndex('uid', ['unique' => true])
            ->addIndex('detail', ['type' => 'fulltext'])
            ->addIndex('cate_id')
            ->create();
    }
}
