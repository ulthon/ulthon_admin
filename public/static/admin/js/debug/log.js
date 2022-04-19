define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'debug.log/index',
        add_url: 'debug.log/add',
        edit_url: 'debug.log/edit',
        delete_url: 'debug.log/delete',
        export_url: 'debug.log/export',
        modify_url: 'debug.log/modify',
    };

    var Controller = {

        index: function () {
            ea.table.render({
                init: init,
                size: 'sm',
                limit: 50,
                cols: [[
                    { type: 'checkbox' },
                    { field: 'id', title: 'id' },
                    { field: 'uid', title: 'uid', minWidth: 140 },
                    { field: 'level', title: 'level' },
                    { field: 'content', title: '日志内容', minWidth: 400, align: 'left', style: 'background-color:#eee' },
                    { field: 'create_time', title: 'create_time', minWidth: 160 },
                    { field: 'app_name', title: 'app_name' },
                    { field: 'controller_name', title: 'controller_name', },
                    { field: 'action_name', title: 'action_name' },
                ]],
                toolbar: [
                    'refresh',
                    'export'
                ]
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