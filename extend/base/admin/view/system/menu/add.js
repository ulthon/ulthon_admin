$(function () {
    iconPickerFa.render({
        elem: '#icon',
        url: PATH_CONFIG.iconLess,
        limit: 12,
        click: function (data) {
            $('#icon').val('fa ' + data.icon);
        },
        success: function (d) {

        }
    });
    autocomplete.render({
        elem: $('#href')[0],
        url: ua.url('system.menu/getMenuTips'),
        template_val: '{{-d.node}}',
        template_txt: '{{-d.node}} <span class=\'layui-badge layui-bg-gray\'>{{-d.title}}</span>',
        onselect: function (resp) {
        }
    });

    ua.listen(function (data) {
        return data;
    }, function (res) {
        ua.msg.success(res.msg, function () {
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
            parent.$('[data-treetable-refresh]').trigger("click");
        });
    });
});