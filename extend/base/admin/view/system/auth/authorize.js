$(function () {
    var tree = layui.tree;

    ua.request.get(
        {
            url: window.location.href,
        }, function (res) {
            res.data = res.data || [];
            tree.render({
                elem: '#node_ids',
                data: res.data,
                showCheckbox: true,
                id: 'nodeDataId',
            });
        }
    );

    ua.listen(function (data) {
        var checkedData = tree.getChecked('nodeDataId');
        var ids = [];
        $.each(checkedData, function (i, v) {
            ids.push(v.id);
            if (v.children !== undefined && v.children.length > 0) {
                $.each(v.children, function (ii, vv) {
                    ids.push(vv.id);
                });
            }
        });
        data.node = JSON.stringify(ids);
        return data;
    });
});