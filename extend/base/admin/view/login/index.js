$(function () {
    if (top.location !== self.location) {
        top.location = self.location;
    }

    $('.bind-password').on('click', function () {
        if ($(this).hasClass('icon-5')) {
            $(this).removeClass('icon-5');
            $("input[name='password']").attr('type', 'password');
        } else {
            $(this).addClass('icon-5');
            $("input[name='password']").attr('type', 'text');
        }
    });

    $('.icon-nocheck').on('click', function () {
        if ($(this).hasClass('icon-check')) {
            $(this).removeClass('icon-check');
        } else {
            $(this).addClass('icon-check');
        }
    });

    $('.login-tip').on('click', function () {
        $('.icon-nocheck').click();
    });

    ua.listen(function (data) {
        data['keep_login'] = $('.icon-nocheck').hasClass('icon-check') ? 1 : 0;
        return data;
    }, function (res) {
        ua.msg.success(res.msg, function () {
            window.location = res.url;
        });
    }, function (res) {
        ua.msg.error(res.msg, function () {
            $('#refreshCaptcha').trigger("click");
        });
    });

    ua.triggerEventReplaceJs('AdminLoginForget', () => {
        $('.forget-password').click(function () {
            layer.msg('可以使用重置密码命令设置:<br/>php think admin:reset:password<br><a target="_blank" href="http://doc.ulthon.com/home/read/ulthon_admin/reset_password/15/16.html"> 参考文档</a>');
        });
    }, (code) => {
        eval(code);
    });
});