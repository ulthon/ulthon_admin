(function () {
    var table = {};

    table.render = function (options) {
        console.log(options);

        var divId = options.elem + 'MB';
        $(options.elem).hide();

        var dataContainer = $(
            `<div id="${divId.substring(1)}'" style="margin:-15px">
            <div class="main-tool"></div>
            <div class="main-data"></div>
            </div>`
        ).insertAfter(options.elem);

        // 暂时隐藏按钮
        // var containerToobar = dataContainer.find('.main-tool');
        // containerToobar.prepend(options.toolbar);

        var containerBox = dataContainer.find('.main-data');

        loadPage();

        $(window).scroll(function () {
            var scrollTop = $(this).scrollTop();
            var windowHeight = $(this).height();

            var scrollHeight = $(document).height();

            if (scrollTop + windowHeight > scrollHeight - windowHeight) {
                loadPage();
            }
        });

        var page = 1;
        var isLoading = false;
        function loadPage() {
            if (isLoading) {
                return;
            }
            isLoading = true;
            loading.show();
            $.get(options.url, { page: page }, function (res) {
                isLoading = false;
                page++;

                loading.hide();

                res.data.forEach(row => {

                    var rowItem = $.extend(true, {}, row);

                    var baseElem = $(baseItem).appendTo(containerBox);

                    if (options.cols[0][0].type == 'checkbox' || options.cols[0[0]].type == 'radio') {
                        $('<input name="layTableCheckbox" type="' + options.cols[0][0].type + '">')
                            .appendTo(baseElem.find('.header').find('.plus'));

                    }

                    rowItem.LAY_COL = $.extend(true, {}, options.cols[0][1]);
                    baseElem.find('.header').find('.main').find('span').html(
                        options.cols[0][1].templet(rowItem)
                    );

                    options.cols[0].forEach(LAY_COL => {
                        var dataItem = $.extend(true, {}, row);
                        dataItem.LAY_COL = $.extend(true, {}, LAY_COL);
                        if (LAY_COL.type == 'checkbox' || LAY_COL.type == 'radio') {
                            return;
                        }

                        if (LAY_COL.templet == ua.table.tool) {
                            // 暂时隐藏按钮
                            // baseElem.find('.footer .plus').html(dataItem.LAY_COL.templet(dataItem));
                            return;
                        }

                        if (LAY_COL.field == 'create_time') {
                            baseElem.find('.footer .main').html(dataItem.LAY_COL.templet(dataItem));
                            return;
                        }

                        var baseDataItemElem = $(baseDataItem).appendTo(baseElem.find('.body .main'));
                        baseDataItemElem.find('.item-title').html(
                            dataItem.LAY_COL.title + ':'
                        );
                        baseDataItemElem.find('.item-value').html(
                            dataItem.LAY_COL.templet(dataItem)
                        );
                    });

                });


            });
        }

        var baseItem = `
            <div class="ul-data-card">
                <div class="header">
                    <div class="main">
                        #
                        <span></span>
                    </div>
                    <div class="plus">
                        
                    </div>
                </div>
                <div class="body">
                    <div class="main">
                        
                        
                    </div>
                </div>
                <div class="footer">
                    <div class="main">
                       
                    </div>
                    <div class="plus">
                    </div>
                </div>
            </div>`;
        var baseDataItem = `
            <div class="item">
                <div class="item-title">
                    
                </div>
                <div class="item-value">
                    
                </div>
            </div>`;

    };

    table.on = function (event, callback) {
        // console.log(event, callback);
    };

    window.uaTable = table;
})();