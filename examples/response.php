<?php

// require '../vendor/autoload.php';
require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Debug;

Debug::enable(1);

define('HACKER_MODE', true);

(new Response())
    ->withStatus(777, 'Lucky Seven')
    #->withStatus(STATUS_UNAUTHORIZED)
    ->withHeader('Engine', 'Irfan\'s Engine v1.0')
    ->write('Hello World!')
    ->send();
