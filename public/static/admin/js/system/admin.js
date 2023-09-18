define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'system.admin/index',
        add_url: 'system.admin/add',
        edit_url: 'system.admin/edit',
        delete_url: 'system.admin/delete',
        modify_url: 'system.admin/modify',
        export_url: 'system.admin/export',
        password_url: 'system.admin/password',
    };

    var authList = ua.getDataBrage('auth_list', []);
    var count = ua.getDataBrage('count', 0);
    var tips = ua.getDataBrage('tips', '');
    var tips = ua.getDataBrage('adminCustomFlag');

    console.log(authList);
    console.log(count);
    console.log(tips);

    var Controller = {

        index: function () {

            ua.table.render({
                init: init,
                cols: [[
                    { type: "checkbox" },
                    { field: 'id', width: 80, title: 'ID' },
                    { field: 'sort', width: 80, title: '排序', edit: 'text' },
                    { field: 'username', minWidth: 80, title: '登录账户' },
                    { field: 'head_img', minWidth: 80, title: '头像', search: false, templet: ua.table.image },
                    { field: 'phone', minWidth: 80, title: '手机' },
                    { field: 'login_num', minWidth: 80, title: '登录次数' },
                    { field: 'remark', minWidth: 80, title: '备注信息', defaultValue: '无' },
                    { field: 'status', title: '状态', width: 85, search: 'select', selectList: { 0: '禁用', 1: '启用' }, templet: ua.table.switch },
                    { field: 'create_time', minWidth: 80, title: '创建时间', search: 'range' },
                    {
                        width: 250,
                        title: '操作',
                        fixed: 'right',
                        templet: ua.table.tool,
                        operat: [
                            'edit',
                            [{
                                text: '设置密码',
                                titleField: 'username',
                                url: init.password_url,
                                method: 'open',
                                auth: 'password',
                                class: 'layui-btn layui-btn-normal layui-btn-xs',
                            }],
                            'delete'
                        ]
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
        password: function () {
            ua.listen();
        }
    };
    return Controller;
});