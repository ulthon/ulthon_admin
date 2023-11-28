$(function () {
    var init = {
        tableElem: '#currentTable',
        tableRenderId: 'currentTableRenderId',
        indexUrl: 'debug.log/index',
        addUrl: 'debug.log/add',
        editUrl: 'debug.log/edit',
        deleteUrl: 'debug.log/delete',
        exportUrl: 'debug.log/export',
        modifyUrl: 'debug.log/modify',
    };

    var uidList = [];
    ua.table.render({
        init: init,
        size: 'sm',
        limit: 50,
        cols: [[
            { type: 'checkbox' },
            { field: 'id', title: 'id', search: 'number_limit' },
            { field: 'id', title: 'id模糊匹配', trueHide: true, fieldAlias: '[id]like' },
            { field: 'id', title: '最大id', trueHide: true, fieldAlias: '[id]max', searchOp: 'max' },
            {
                field: 'uid', title: 'uid', minWidth: 120,
            },
            { field: 'level', title: 'level', minWidth: 70 },
            {
                field: 'content', title: '日志内容', minWidth: 450, align: 'left', templet: function (data) {

                    if (uidList.indexOf(data.uid) < 0) {
                        uidList.push(data.uid);
                    }
                    var currentUidIndex = uidList.indexOf(data.uid);

                    var className = 'log-group log-group-' + (currentUidIndex % 2);
                    return '<div class="' + className + '">' + data.content + '</div>';
                }
            },
            { field: 'create_time', title: '记录时间', minWidth: 160, search: 'time_limit' },
            { field: 'app_name', title: 'app_name' },
            { field: 'controller_name', title: 'controller_name', },
            { field: 'action_name', title: 'action_name' },
        ]],
        toolbar: [
            'refresh',
            'export'
        ]
    });

    ua.listen();
});