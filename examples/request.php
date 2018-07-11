<?php

// require '../vendor/autoload.php';
require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Debug;

Debug::enable(1);

define('HACKER_MODE', true);

// Note: SMART is not an allowed method, but since the HACKER_MODE is set to true,
// it will construct the request
$request = new Request 
(
    [
        'method' => 'SMART', 
        'uri'    => 'http://example.com'
    ]
);

# $request->withRequestTarget('test/world')
$request->withHeader('Engine', 'Irfan\'s Engine v1.0');

Debug::dump($request);
Debug::dump((string) $request->getUri());
