define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'mall.tag/index',
        add_url: 'mall.tag/add',
        edit_url: 'mall.tag/edit',
        delete_url: 'mall.tag/delete',
        export_url: 'mall.tag/export',
        modify_url: 'mall.tag/modify',
    };

    var Controller = {

        index: function () {
            ua.table.render({
                init: init,
                cols: [[
                    {type: 'checkbox'},
                    {field: 'id', title: 'id'},
                    {field: 'title', title: '名称'},
                    {width: 250, title: '操作', templet: ua.table.tool},
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
    };
    return Controller;
});