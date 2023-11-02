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

        // 如果field没有key或title，则初始化
        for (var i = 0; i < options.field.length; i++) {
            if (!options.field[i].key) {
                options.field[i].key = 'name';
            }
            if (!options.field[i].title) {
                options.field[i].title = '名称';
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

        app = new Vue({
            el: elem,
            data() {
                return {
                    setting: options,
                    value: '',
                    listItem: [

                    ],
                    originalValue: '',
                    field: options.field
                };
            },

            created() {
                if (this.setting.value) {
                    this.originalValue = $.extend(true, [], this.setting.value);
                    this.listItem = this.setting.value;
                    this.value = this.setting.value;
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
                },
                removeItem(item, index) {
                    this.listItem.splice(index, 1);
                },
                onAddItem() {

                    var emptyItem = {};

                    for (var i = 0; i < this.field.length; i++) {
                        emptyItem[this.field[i].key] = '';
                    }

                    this.listItem.push(emptyItem);
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