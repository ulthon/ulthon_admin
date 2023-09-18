define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'mall.goods/index',
        add_url: 'mall.goods/add',
        edit_url: 'mall.goods/edit',
        delete_url: 'mall.goods/delete',
        export_url: 'mall.goods/export',
        modify_url: 'mall.goods/modify',
        stock_url: 'mall.goods/stock',
        read_url: 'mall.goods/read',
        formFullScreen: true,
    };

    var Controller = {

        index: function () {
            ua.table.render({
                init: init,
                totalRow: true,
                cols: [[
                    { type: "checkbox" },
                    { field: 'id', width: 80, title: 'ID', totalRowText: '合计：' },
                    { field: 'sort', width: 80, title: '排序', edit: 'text' },
                    { field: 'cate_id', minWidth: 80, title: '商品分类', selectList: ua.getDataBrage('select_list_cate'), selectValue: 'id', selectLabel: 'title', defaultSearchValue: ua.getQueryVariable('cate_id', '') },
                    { field: 'cate.image', minWidth: 80, title: '分类图片', search: false, templet: ua.table.image },
                    { field: 'title', minWidth: 80, title: '商品名称', },
                    { field: 'logo', minWidth: 80, title: '商品图片', search: false, templet: ua.table.image },
                    { field: 'market_price', width: 100, title: '市场价', templet: ua.table.price },
                    {
                        field: 'discount_price', width: 120, title: '折扣价', templet: ua.table.price, totalRow: true,
                        templet: ua.table.copyText,
                        valueParser(value) {
                            return '￥' + value
                        },
                        copyText(value, data) {
                            return data.discount_price;
                        }
                    },
                    { field: 'total_stock', width: 100, title: '库存统计', totalRow: '{{= parseInt(d.TOTAL_NUMS) }} 个' },
                    {
                        field: 'stock',
                        width: 100,
                        title: '剩余库存',
                        valueParser(value, data) {
                            if (value >= 20) {
                                return value;
                            }
                            return value + '(缺货)'
                        }
                    },
                    { field: 'virtual_sales', width: 100, title: '虚拟销量' },
                    { field: 'sales', width: 80, title: '销量' },
                    { field: 'status', title: '状态', width: 85, selectList: { 0: '禁用', 1: '启用' }, templet: ua.table.switch },
                    { field: 'create_time', minWidth: 80, title: '创建时间', search: 'range' },
                    {
                        width: 250,
                        title: '操作',
                        templet: ua.table.tool,
                        fixed: 'right',
                        operat: [
                            'edit',
                            [{
                                text: '入库',
                                url: init.stock_url,
                                method: 'open',
                                auth: 'stock',
                                class: 'layui-btn layui-btn-xs layui-btn-normal',
                            }, {
                                text: '详情',
                                url: init.read_url,
                                method: 'open',
                                auth: 'edit',
                                extend: 'data-full="true"',
                                class: 'layui-btn layui-btn-xs layui-btn-primary',
                            },],
                            'delete']
                    }
                ]],
            });

            ua.listen();
        },
        add: function () {
            ua.listen();
        },
        edit: function () {
            ua.listen();
        },
        stock: function () {
            ua.listen();
        },
        read: function () {
            ua.listen();
        },
    };
    return Controller;
});