$(function () {
    var form = layui.form;
    $('.show-type-item').hide();
    $('.show-type-item.' + upload_type).show();
    form.on("radio(upload_type)", function (data) {

        $('.show-type-item').hide();
        $('.show-type-item.' + this.value).show();
    });

    ua.listen();
});