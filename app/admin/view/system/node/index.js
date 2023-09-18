$(function () {

    ua.table.render({
        init: init,
        search: false,
        page: false,
        toolbar: ['refresh',
            [{
                text: '更新节点',
                title: '确定更新新节点？',
                url: 'system.node/refreshNode?force=0',
                method: 'request',
                auth: 'refresh',
                class: 'layui-btn layui-btn-success layui-btn-sm',
                icon: 'fa fa-hourglass',
                extend: 'data-table="' + init.table_render_id + '"',
            }, {
                text: '强制更新节点',
                title: '该操作会覆盖已存在的节点信息。<br>确定强制更新节点？',
                url: 'system.node/refreshNode?force=1',
                method: 'request',
                auth: 'refresh',
                class: 'layui-btn layui-btn-sm layui-btn-normal',
                icon: 'fa fa-hourglass',
                extend: 'data-table="' + init.table_render_id + '"',
            }, {

                text: '清除失效节点',
                title: '确定清除失效节点？',
                url: 'system.node/clearNode',
                method: 'request',
                auth: 'clear',
                class: 'layui-btn layui-btn-sm layui-btn-danger',
                icon: 'fa fa-trash-o',
                extend: 'data-table="' + init.table_render_id + '"',
            }
            ]],
        cols: [[
            { field: 'node', sort: false, minWidth: 200, align: 'left', title: '系统节点' },
            { field: 'title', sort: false, minWidth: 80, title: '节点名称 <i class="table-edit-tips color-red">*</i>', edit: 'text' },
            { field: 'update_time', sort: false, minWidth: 80, title: '更新时间', search: 'range' },
            { field: 'is_auth', sort: false, title: '节点控制', width: 85, search: 'select', selectList: { 0: '禁用', 1: '启用' }, templet: ua.table.switch },
        ]],
    });

    ua.listen();
});