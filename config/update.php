<?php

$config = [];

$skip_files = [];

$skip_files[] = '/app/common/app/functions.php';
$skip_files[] = '/app/common/app/listen.php';
$skip_files[] = '/app/common/app/middleware.php';
$skip_files[] = '/app/common/app/service.php';

$config['skip_files'] = $skip_files;

$skip_dir = [];
$skip_dir[] = '/runtime';
$skip_dir[] = '/vendor';

$config['skip_dir'] = $skip_dir;

return $config;
