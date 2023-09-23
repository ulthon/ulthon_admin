<?php

$config = [];

$skip_files = [];

$skip_files[] = 'app/common/app/functions.php';
$skip_files[] = 'app/common/app/listen.php';
$skip_files[] = 'app/common/app/middleware.php';
$skip_files[] = 'app/common/app/service.php';
$skip_files[] = 'composer.lock';
$skip_files[] = 'README.md';
$skip_files[] = 'README.en.md';
$skip_files[] = 'composer.json';

$config['skip_files'] = $skip_files;

$skip_dir = [];
$skip_dir[] = 'runtime';
$skip_dir[] = 'vendor';
$skip_dir[] = 'app/admin/controller';
$skip_dir[] = 'app/admin/middleware';
$skip_dir[] = 'app/admin/model';
$skip_dir[] = 'app/admin/service';
$skip_dir[] = 'app/admin/traits';

$config['skip_dir'] = $skip_dir;

// append 如果当前版本不存在，则追加，如果存在，则不应当覆盖
// append 的文件应当在skip内部
$config['append_files'] = [];

$append_dir = [];
$append_dir[] = 'app/admin/controller';
$append_dir[] = 'app/admin/middleware';
$append_dir[] = 'app/admin/model';
$append_dir[] = 'app/admin/service';
$append_dir[] = 'app/admin/traits';

$config['append_dir'] = $append_dir;

return $config;
