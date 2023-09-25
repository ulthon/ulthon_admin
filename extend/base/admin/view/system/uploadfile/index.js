$(function () {

    ua.table.render({
        init: init,
        cols: [[
            { type: "checkbox" },
            { field: 'id', width: 80, title: 'ID' },
            { field: 'upload_type', minWidth: 80, title: '存储位置', search: 'select', selectList: { 'local': '本地', 'alioss': '阿里云', 'qnoss': '七牛云', ',txcos': '腾讯云' } },
            { field: 'url', minWidth: 80, search: false, title: '文件预览', templet: ua.table.filePreview },
            {
                field: 'url', minWidth: 120, title: '保存地址', templet: ua.table.url, urlNameField: function (data) {
                    return data.url;
                }
            },
            { field: 'original_name', minWidth: 80, title: '文件原名' },
            { field: 'mime_type', minWidth: 80, title: 'mime类型' },
            { field: 'file_ext', minWidth: 80, title: '文件后缀' },
            { field: 'create_time', minWidth: 80, title: '创建时间', search: 'range' },
            {
                width: 250, title: '操作', templet: ua.table.tool, operat: ['delete'], fixed: 'right', hide: function () {
                    var selectMode = ua.getQueryVariable("select_mode");

                    console.log(selectMode);
                    if (selectMode == 'radio' || selectMode == 'checkbox') {
                        return true;
                    }
                    return false;
                }
            }
        ]],
    });

    ua.listen();
});