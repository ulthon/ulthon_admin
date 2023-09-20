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

$config['skip_dir'] = $skip_dir;

return $config;
