$(function () {
    iconPickerFa.render({
        elem: '#icon',
        url: PATH_CONFIG.iconLess,
        limit: 12,
        click: function (data) {
            $('#icon').val('fa ' + data.icon);
        },
        success: function (d) {
            console.log(d);
        }
    });
    autocomplete.render({
        elem: $('#href')[0],
        url: ua.url('system.menu/getMenuTips'),
        template_val: '{{d.node}}',
        template_txt: '{{d.node}} <span class=\'layui-badge layui-bg-gray\'>{{d.title}}</span>',
        onselect: function (resp) {
        }
    });

    ua.listen();
});