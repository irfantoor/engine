<?php

include 'autoload.php';

if (is_file(__DIR__ . '/.maintenance')) {
    include APP . 'Views/maintenance.php';
    exit;
}

$config = require 'config.php';
$ie = new IrfanTOOR\Engine($config);

$ie->addRoute(
    ['GET', 'POST'], # methods
    'admin(/.*)?',  # path patern to match to
    'App\Controller\AdminController'
);

$ie->addRoute(
    'GET', # method
    '.*',  # path patern to match to
    'App\Controller\WelcomeController'
);

$ie->run();
