<?php

// require '../vendor/autoload.php';
require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Debug;

Debug::enable(1);

$env = new Environment(['HELLO' => 'World!']);
Debug::dump($env);
