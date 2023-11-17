(function () {
    const propertyInputCss = '/static/plugs/lay-module/propertyInput/propertyInput.css';
    const propertyInputHtml = '/static/plugs/lay-module/propertyInput/propertyInput.html';

    var propertyInput = function () {
        $(function () {
            var cssElement = document.createElement('link');

            cssElement.setAttribute('rel', 'stylesheet');

            cssElement.setAttribute('href', propertyInputCss);

            document.body.appendChild(cssElement);
        });
    };
    var propertyInputTemplate = '';

    $.ajax({
        type: "get",
        url: propertyInputHtml,
        cache: true,
        async: false,
        success: function (template) {
            propertyInputTemplate = template;
        }

    });

    propertyInput.prototype.render = function (elem, data) {

        var defaultOption = {
            placeholder: '请选择',
            required: ''
        };

        var options = $.extend(defaultOption, data);

        options.value = $.trim(options.value);
        // 处理field参数
        // 如果是字符串，则转为json
        if (typeof options.field === 'string') {
            try {
                options.field = JSON.parse(options.field);
            } catch (e) {
                try {
                    options.field = eval('(' + options.field + ')');
                } catch (e) {
                    options.field = [];
                    console.error('field参数格式错误');
                }
            }
        }

        // 如果没有定义，则初始化
        if (!options.field) {
            options.field = [
                {
                    key: 'name',
                    title: '名称'
                },
                {
                    key: 'value',
                    title: '数据'
                }
            ];
        } else {
            // 如果是对象，则转成数组
            if (typeof options.field === 'object' && !Array.isArray(options.field)) {
                var field = [];
                for (var key in options.field) {
                    field.push({
                        key: key,
                        title: options.field[key]
                    });
                }
                options.field = field;
            } else if (!Array.isArray(options.field) || options.field.length < 1) {
                // 如果field不符合规则，则初始化
                options.field = [
                    {
                        key: 'name',
                        title: '名称'
                    },
                    {
                        key: 'value',
                        title: '数据'
                    }
                ];
            }
        }

        // 如果field没有key、title、type、default，则初始化
        for (var i = 0; i < options.field.length; i++) {
            if (!options.field[i].key) {
                options.field[i].key = 'name';
            }
            if (!options.field[i].title) {
                options.field[i].title = '名称';
            }
            if (!options.field[i].type) {
                options.field[i].type = 'text';
            }
            if (!options.field[i].default) {
                options.field[i].default = '';
            }



            // 如果type是textarea且没有定义height，则高度为40px
            if (options.field[i].type == 'textarea' && !options.field[i].height) {
                options.field[i].height = '40px';
            }

            // 如果type是radio
            if (options.field[i].type == 'radio') {

                // selectList支持以下三种形式的数据
                // 1 ['string1','string2']
                // 2 [{title:'string1',value:'string1'},{title:'string2',value:'string2'}]
                // 3 {string1:'string1',string2:'string2'}
                // 第2个是标准格式


                // 如果没有定义selectList
                if (!options.field[i].selectList) {
                    options.field[i].selectList = [
                        {
                            title: '是',
                            value: 1
                        },
                        {
                            title: '否',
                            value: 0
                        }
                    ];
                }

                // 如果selectList是数组，但是数组的元素是字符串，则将该元素转为对象，title和value都为该元素
                if (Array.isArray(options.field[i].selectList)) {
                    for (var j = 0; j < options.field[i].selectList.length; j++) {
                        if (typeof options.field[i].selectList[j] === 'string') {
                            options.field[i].selectList[j] = {
                                title: options.field[i].selectList[j],
                                value: options.field[i].selectList[j]
                            };
                        }
                    }
                } else if (typeof options.field[i].selectList === 'object') {
                    // 如果selectList是对象，则将该对象转为数组
                    var selectList = [];
                    for (var key in options.field[i].selectList) {
                        selectList.push({
                            title: options.field[i].selectList[key],
                            value: key
                        });
                    }
                    options.field[i].selectList = selectList;
                }

                // selectList必须包含title和value，如果没有则报错
                for (var j = 0; j < options.field[i].selectList.length; j++) {
                    if (!options.field[i].selectList[j].title || !options.field[i].selectList[j].value) {
                        console.error('selectList的元素必须包含title和value');
                    }
                }

            }
        }

        // 处理value参数
        // 如果是字符串，则转为json
        if (typeof options.value === 'string') {
            try {
                options.value = JSON.parse(options.value);
            } catch (e) {
                options.value = [];
            }
        }

        // 如果value缺少uid，则生成
        for (var i = 0; i < options.value.length; i++) {
            if (!options.value[i].uid) {
                options.value[i].uid = ua.randdomString();
            }
        }

        app = new Vue({
            el: elem,
            data() {
                return {
                    setting: options,
                    value: '',
                    listItem: [

                    ],
                    originalValue: '',
                    field: options.field,
                };
            },

            created() {
                if (this.setting.value) {
                    this.originalValue = $.extend(true, [], this.setting.value);
                    this.listItem = $.extend(true, [], this.setting.value);
                    this.updateValue(this.listItem);

                    if (this.listItem.length == 0) {
                        this.onAddItem();
                    }
                }

            },
            mounted() {
                if (options.required == 1) {
                    $(this.$refs['propertyInput']).closest('.layui-form-item').children('.layui-form-label').addClass('required');
                }
            },
            template: propertyInputTemplate,

            methods: {
                onResetItem() {
                    this.listItem = $.extend(true, [], this.originalValue);
                    if (this.listItem.length == 0) {
                        this.onAddItem();
                    }

                    this.updateValue(this.listItem);
                },
                onAddItem() {

                    var emptyItem = {};

                    emptyItem.uid = ua.randdomString();

                    for (var i = 0; i < this.field.length; i++) {
                        emptyItem[this.field[i].key] = this.field[i].default;
                    }

                    this.listItem.push(emptyItem);

                    this.updateValue(this.listItem);
                },
                onItemChange(item, index, itemField, event) {
                    this.listItem[index][itemField.key] = event.target.value;

                    this.updateValue(this.listItem);
                },
                onItemDelete(item, index) {
                    if (this.listItem.length <= 1) {
                        this.listItem = [
                            {
                                name: '',
                                value: ''
                            }
                        ];
                    } else {
                        this.listItem.splice(index, 1);
                    }

                    this.updateValue(this.listItem);
                },
                updateValue(newValue) {
                    this.value = JSON.stringify(newValue);
                }
            }
        });

        return app;
    };

    window.propertyInput = new propertyInput();
})();