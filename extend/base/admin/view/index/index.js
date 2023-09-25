$(function () {
    var options = {
        iniUrl: ua.url('ajax/initAdmin'),    // 初始化接口
        clearUrl: ua.url("ajax/clearCache"), // 缓存清理接口
        urlHashLocation: true,      // 是否打开hash定位
        bgColorDefault: false,      // 主题默认配置
        multiModule: true,          // 是否开启多模块
        menuChildOpen: false,       // 是否默认展开菜单
        loadingTime: 0,             // 初始化加载时间
        pageAnim: true,             // iframe窗口动画
        maxTabNum: 20,              // 最大的tab打开数量
    };
    miniAdmin.render(options);

    $('.login-out').on("click", function () {
        ua.request.get({
            url: 'login/out',
            prefix: true,
        }, function (res) {
            ua.msg.success(res.msg, function () {
                window.location = ua.url('login/index');
            });
        });
    });
});