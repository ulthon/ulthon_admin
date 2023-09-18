<?php

$config = [];

$skip_files = [];

$skip_files[] = '/app/common/app/functions.php';
$skip_files[] = '/app/common/app/listen.php';
$skip_files[] = '/app/common/app/middleware.php';
$skip_files[] = '/app/common/app/service.php';

$config['skip_files'] = $skip_files;

return $config;
