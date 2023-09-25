<?php

$config = [];

$skip_files = [];

$skip_files[] = 'app/common/app/functions.php';
$skip_files[] = 'app/common/app/listen.php';
$skip_files[] = 'app/common/app/middleware.php';
$skip_files[] = 'app/common/app/service.php';
$skip_files[] = 'app/common/app/provider.php';
$skip_files[] = 'composer.lock';
$skip_files[] = 'README.md';
$skip_files[] = 'README.en.md';
$skip_files[] = 'composer.json';

$skip_files[] = 'app/common/event/AdminLoginSuccess/LogEvent.php';
$skip_files[] = 'app/common/event/AdminLoginType/DemoEvent.php';

$config['skip_files'] = $skip_files;

$skip_dir = [];

$skip_dir[] = 'runtime';
$skip_dir[] = 'vendor';

$skip_dir[] = 'app/admin/controller';
$skip_dir[] = 'app/admin/middleware';
$skip_dir[] = 'app/admin/model';
$skip_dir[] = 'app/admin/service';
$skip_dir[] = 'app/admin/traits';
$skip_dir[] = 'app/admin/view';

$skip_dir[] = 'app/common/controller';
$skip_dir[] = 'app/common/command';
$skip_dir[] = 'app/common/constants';
$skip_dir[] = 'app/common/exception';
$skip_dir[] = 'app/common/model';
$skip_dir[] = 'app/common/provider';
$skip_dir[] = 'app/common/service';
$skip_dir[] = 'app/common/tools';
$skip_dir[] = 'app/common/traits';
$skip_dir[] = 'app/common/tpl';

$config['skip_dir'] = $skip_dir;

// append 如果当前版本不存在，则追加，如果存在，则不应当覆盖
// append 的文件应当在skip内部

$append_files = [];

$append_files[] = 'app/common/app/functions.php';
$append_files[] = 'app/common/app/listen.php';
$append_files[] = 'app/common/app/middleware.php';
$append_files[] = 'app/common/app/service.php';
$append_files[] = 'app/common/app/provider.php';

$append_files[] = 'app/common/event/AdminLoginSuccess/LogEvent.php';
$append_files[] = 'app/common/event/AdminLoginType/DemoEvent.php';

$config['append_files'] = $append_files;

$append_dir = [];

$append_dir[] = 'app/admin/controller';
$append_dir[] = 'app/admin/middleware';
$append_dir[] = 'app/admin/model';
$append_dir[] = 'app/admin/service';
$append_dir[] = 'app/admin/traits';
$append_dir[] = 'app/admin/view';

$append_dir[] = 'app/common/controller';
$append_dir[] = 'app/common/command';
$append_dir[] = 'app/common/constants';
$append_dir[] = 'app/common/exception';
$append_dir[] = 'app/common/model';
$append_dir[] = 'app/common/provider';
$append_dir[] = 'app/common/service';
$append_dir[] = 'app/common/tools';
$append_dir[] = 'app/common/traits';
$append_dir[] = 'app/common/tpl';

$config['append_dir'] = $append_dir;

return $config;
