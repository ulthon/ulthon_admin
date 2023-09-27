
const loading = {};
loading.index = 0;
loading.showCount = 0;
loading.show = function (count) {

    if (typeof count == 'undefined') {
        count = 1;
    }

    if (loading.showCount == 0) {
        loading.index = layer.load();
    }

    loading.showCount += count;
};

loading.hide = function (count) {


    if (typeof count == undefined) {
        count = 1;
    }

    if (count === true) {
        count = 1;
        loading.showCount = 0;
    }

    loading.showCount -= 1;

    if (loading.showCount < 0) {
        loading.showCount = 0;
    }

    if (loading.showCount == 0) {
        layer.close(loading.index);
    }

};

const tools = {};

tools.checkMobile = function () {
    var userAgentInfo = navigator.userAgent;
    var mobileAgents = ["Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"];
    var mobile_flag = false;
    //根据userAgent判断是否是手机
    for (var v = 0; v < mobileAgents.length; v++) {
        if (userAgentInfo.indexOf(mobileAgents[v]) > 0) {
            mobile_flag = true;
            break;
        }
    }
    var screen_width = window.screen.width;
    var screen_height = window.screen.height;
    //根据屏幕分辨率判断是否是手机
    if (screen_width < 600 && screen_height < 800) {
        mobile_flag = true;
    }
    return mobile_flag;
};