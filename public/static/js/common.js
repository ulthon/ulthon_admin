function randomString(len) {
  len = len || 32;
  var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
  var maxPos = $chars.length;
  var pwd = '';
  for (i = 0; i < len; i++) {
    pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
  }
  return pwd;
}

function renderUpload(target, params) {

  if (typeof params == "undefined") {
    params = {}
  }
  if (typeof params.upload == "undefined") {
    var upload = layui.upload
  } else {
    var upload = params.upload
  }

  if (typeof params.type == 'undefined') {
    var type = target
  } else {
    var type = params.type
  }

  if(typeof window.uploadAddressPrefix == 'undefined'){
    window.uploadAddressPrefix = '/api/'
  }

  if (typeof params.url == 'undefined') {
    var url = window.uploadAddressPrefix + 'File/save'
  } else {
    var url = params.url
  }
  if (typeof params.accept == 'undefined') {
    var accept = 'images'
  } else {
    var accept = params.accept
  }
  if (typeof params.acceptMime == 'undefined') {
    var acceptMime = 'image/png,image/jpeg,image/gif'
  } else {
    var acceptMime = params.acceptMime
  }
  if (typeof params.isRenderInputAndShow == 'undefined') {
    var isRenderInputAndShow = true
  } else {
    var isRenderInputAndShow = params.isRenderInputAndShow
  }
  if (typeof params.done == 'undefined') {
    var done = function (result) {
      layer.close(window.uploading)
      if (result.code == 0) {
        layer.msg('上传成功');
        if (isRenderInputAndShow) {
          $('.cancle-' + target).show()
          $('.input-' + target).val(result.data.save_name);
          $('.preview-' + target).attr('src', result.data.src).show();
        }
      } else {
        layer.msg(result.msg)
      }
    }
  } else {
    var done = params.done
  }

  if (typeof params.exts == 'undefined') {
    var exts = ''
  } else {
    var exts = params.exts
  }

  if (accept == 'video') {
    if (exts.length == 0) {
      exts = 'mp4'
    }
  }

  if (isRenderInputAndShow) {
    if ($('.input-' + target).val().length == '') {
      $('.cancle-' + target).hide()
    } else {
      $('.cancle-' + target).show()
    }
    $('.cancle-' + target).click(function () {
      $('.input-' + target).val('');
      $('.preview-' + target).hide();
      $(this).hide()
    })
  }

  return upload.render({
    elem: '.upload-' + target,
    url: url,
    data: {
      type: type,
    },
    accept: accept,
    acceptMime: acceptMime,
    before: function () {
      window.uploading = layer.load()
    },
    done: done,
    error: function () {
      layer.close(window.uploading)
    }
  })
}


const loading = {};
loading.index = 0;
loading.show = function () {
  if (loading.index != 0) {
    layer.close(loading.index)
  }

  loading.index = layer.load()
}

loading.hide = function () {
  layer.close(loading.index);
  loading.index = 0;
}

$(function () {
  $('[data-href]').click(function () {
    var item = this;

    loading.show()

    var href = $(item).data('href')

    location.href = href
  })
})

function isPC() {
  var userAgentInfo = navigator.userAgent;
  var Agents = ["Android", "iPhone",
    "SymbianOS", "Windows Phone",
    "iPad", "iPod"];
  var flag = true;
  for (var v = 0; v < Agents.length; v++) {
    if (userAgentInfo.indexOf(Agents[v]) > 0) {
      flag = false;
      break;
    }
  }
  return flag;
}

top.onbeforeunload = function (e) {
  setTimeout(() => {
    loading.show()
  }, 2000);
}