define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'test.goods/index',
        add_url: 'test.goods/add',
        edit_url: 'test.goods/edit',
        delete_url: 'test.goods/delete',
        export_url: 'test.goods/export',
        modify_url: 'test.goods/modify',
    };

    var Controller = {

        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: 'checkbox'},                    {field: 'id', title: 'id'},                    {field: 'cate_id', title: '分类ID'},                    {field: 'title', title: '商品名称'},                    {field: 'logo', title: '商品logo', templet: ea.table.image},                    {field: 'total_stock', title: '总库存'},                    {field: 'sort', title: '排序', edit: 'text'},                    {field: 'status', search: 'select', selectList: ea.getDataBrage('select_list_status'), title: '状态', templet: ea.table.switch},                    {field: 'cert_file', title: '合格证', templet: ea.table.url},                    {field: 'remark', title: '备注说明', templet: ea.table.text},                    {field: 'create_time', title: 'create_time'},                    {field: 'publish_time', title: '发布日期'},                    {field: 'sale_time', title: '售卖日期'},                    {field: 'intro', title: '简介'},                    {field: 'time_status', search: 'select', selectList: ea.getDataBrage('select_list_time_status'), title: '秒杀状态'},                    {field: 'is_recommend', search: 'select', selectList: ea.getDataBrage('select_list_is_recommend'), title: '是否推荐'},                    {field: 'shop_type', search: 'select', selectList: ea.getDataBrage('select_list_shop_type'), title: '商品类型'},                    {field: 'from_area', title: '产地'},                    {field: 'store_city', title: '仓库'},                    {field: 'mallCate.id', title: ''},                    {field: 'mallCate.title', title: '分类名'},                    {field: 'mallCate.image', title: '分类图片', templet: ea.table.image},                    {field: 'mallCate.sort', title: '排序', edit: 'text'},                    {field: 'mallCate.status', title: '状态', templet: ea.table.switch},                    {field: 'mallCate.remark', title: '备注说明', templet: ea.table.text},                    {field: 'mallCate.create_time', title: '创建时间'},                    {width: 250, title: '操作', templet: ea.table.tool},
                ]],
            });

            ea.listen();
        },
        add: function () {
            ea.listen();
        },
        edit: function () {
            ea.listen();
        },
    };
    return Controller;
});