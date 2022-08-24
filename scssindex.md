需要编译的scss文件：
```
public/static/common/css/theme/*.scss
public/static/plugs/lay-module/tableData/tableData.scss
public/static/plugs/lay-module/tagInput/tagInput.scss
```

vscode中liveSassCompiler的配置:

```json
{
    "liveSassCompile.settings.includeItems": [
        "/public/static/common/css/theme/*.scss",
        "/public/static/plugs/lay-module/tableData/tableData.scss",
        "/public/static/plugs/lay-module/tagInput/tagInput.scss"
    ]
}
```