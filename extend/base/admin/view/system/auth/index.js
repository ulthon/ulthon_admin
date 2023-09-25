$(function () {

    ua.table.render({
        init: init,
        cols: [[
            { type: "checkbox" },
            { field: 'id', width: 80, title: 'ID' },
            { field: 'sort', width: 80, title: '排序', edit: 'text' },
            { field: 'title', minWidth: 80, title: '权限名称' },
            { field: 'remark', minWidth: 80, title: '备注信息' },
            { field: 'status', title: '状态', width: 85, search: 'select', selectList: { 0: '禁用', 1: '启用' }, templet: ua.table.switch },
            { field: 'create_time', minWidth: 80, title: '创建时间', search: 'range' },
            {
                width: 250,
                title: '操作',
                templet: ua.table.tool,
                operat: [
                    'edit',
                    [{
                        text: '授权',
                        url: init.authorize_url,
                        method: 'open',
                        auth: 'authorize',
                        class: 'layui-btn layui-btn-normal layui-btn-xs',
                    }],
                    'delete'
                ]
            }
        ]],
    });

    ua.listen();
});