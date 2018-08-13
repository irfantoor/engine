<?php

define ('ROOT', dirname(__DIR__)  . '/');
define ('APP',  dirname(__FILE__) . '/');

# Irfan's Engine Classes
require ROOT . 'vendor/irfantoor/engine/src/Autoloader.php';

$loader = new IrfanTOOR\Engine\Autoloader;
$loader->register();
$loader->addNamespace('App\\Command',           APP . 'command/');
$loader->addNamespace('App\\Controller',        APP . 'controller/');
$loader->addNamespace('App\\Controller\\Admin', APP . 'controller/admin/');
$loader->addNamespace('App\\Middleware',        APP . 'middleware/');
$loader->addNamespace('App\\Model',             APP . 'model/');
$loader->addNamespace('App\\View',              APP . 'view/');

# Other Vendor's
require ROOT . 'vendor/autoload.php';

use Tracy\Debugger;
Debugger::enable();
