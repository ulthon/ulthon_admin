$(function () {
    ua.table.render({
        init: init,
        cols: [[
            { type: 'checkbox' },
            { field: 'id', title: 'id' },
            { field: 'title', title: '名称' },
            { width: 250, title: '操作', templet: ua.table.tool },
        ]],
    });

    ua.listen();
});