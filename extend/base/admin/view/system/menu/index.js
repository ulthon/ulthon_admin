$(function () {

    var renderTable = function () {
        layer.load(2);
        treetable.render({
            treeColIndex: 1,
            treeSpid: 0,
            homdPid: 99999999,
            treeIdName: 'id',
            treePidName: 'pid',
            url: ua.url(init.indexUrl),
            elem: init.tableElem,
            id: init.tableRenderId,
            toolbar: '#toolbar',
            page: false,
            skin: 'line',

            // @todo 不直接使用ua.table.render(); 进行表格初始化, 需要使用 ua.table.formatCols(); 方法格式化`cols`列数据
            cols: ua.table.formatCols([[
                { type: 'checkbox' },
                { field: 'title', sort: false, width: 250, title: '菜单名称', align: 'left' },
                { field: 'icon', sort: false, width: 80, title: '图标', templet: ua.table.icon },
                { field: 'href', sort: false, minWidth: 120, title: '菜单链接' },
                {
                    field: 'is_home', sort: false,
                    width: 80,
                    title: '类型',
                    templet: function (d) {
                        if (d.pid === 99999999) {
                            return '<span class="layui-badge layui-bg-blue">首页</span>';
                        }
                        if (d.pid === 0) {
                            return '<span class="layui-badge layui-bg-gray">模块</span>';
                        } else {
                            return '<span class="layui-badge-rim">菜单</span>';
                        }
                    }
                },
                { field: 'status', sort: false, title: '状态', width: 85, templet: ua.table.switch },
                { field: 'sort', sort: false, width: 80, title: '排序', edit: 'text' },
                {
                    width: 220,
                    title: '操作',
                    fixed: 'right',
                    templet: ua.table.tool,
                    operat: [
                        [{
                            text: '添加下级',
                            url: init.addUrl,
                            method: 'open',
                            auth: 'add',
                            class: 'layui-btn layui-btn-xs layui-btn-normal',
                            extend: 'data-full="true"',
                            _if: function (data) {
                                if (data.pid == 99999999) {
                                    return false;
                                }

                                return true;
                            }

                        }, {
                            text: '编辑',
                            url: init.editUrl,
                            method: 'open',
                            auth: 'edit',
                            class: 'layui-btn layui-btn-xs layui-btn-success',
                            extend: 'data-full="true"',
                            _if: 'status'
                        }, {
                            text: '删除',
                            method: 'none',
                            auth: 'delete',
                            class: 'layui-btn layui-btn-xs layui-btn-danger',
                            extend: 'data-treetable-delete-item="1" data-url="' + init.deleteUrl + '"',
                            data: ['id', 'title'],
                            _if(data) {

                                if (data.pid == ua.getDataBrage('menu_home_pid')) {
                                    return false
                                }

                                return true;
                            }
                        },],

                    ]
                }
            ]], init),
            done: function () {
                layer.closeAll('loading');

                $(".layui-table-main tr").each(function (index, val) {
                    $(".layui-table-fixed").each(function () {
                        $($(this).find(".layui-table-body tbody tr")[index]).height($(val).height());
                    });
                });
            }
        });
    };

    renderTable();

    $('body').on('click', '[data-treetable-refresh]', function () {
        renderTable();
    });

    $('body').on('click', '[data-treetable-delete-item]', function () {
        var id = $(this).data('id');
        var url = $(this).attr('data-url');
        url = url != undefined ? ua.url(url) : window.location.href;
        ua.msg.confirm('确定删除？', function () {
            ua.request.post({
                url: url,
                data: {
                    id: id
                },
            }, function (res) {
                ua.msg.success(res.msg, function () {
                    renderTable();
                });
            });
        });
        return false;
    })

    $('body').on('click', '[data-treetable-delete]', function () {
        var tableId = $(this).attr('data-treetable-delete'),
            url = $(this).attr('data-url');
        tableId = tableId || init.tableRenderId;
        url = url != undefined ? ua.url(url) : window.location.href;
        var checkStatus = table.checkStatus(tableId),
            data = checkStatus.data;
        if (data.length <= 0) {
            ua.msg.error('请勾选需要删除的数据');
            return false;
        }
        var ids = [];
        $.each(data, function (i, v) {
            ids.push(v.id);
        });
        ua.msg.confirm('确定删除？', function () {
            ua.request.post({
                url: url,
                data: {
                    id: ids
                },
            }, function (res) {
                ua.msg.success(res.msg, function () {
                    renderTable();
                });
            });
        });
        return false;
    });

    ua.table.listenSwitch({ filter: 'status', url: init.modifyUrl });

    ua.table.listenEdit(init, 'currentTable', init.tableRenderId, false);

    ua.listen();
});