<?php

require 'autoload.php';

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
$request = $request->withHeader('Engine', 'Irfan\'s Engine v' . IrfanTOOR\Engine::VERSION);

d($request);
dd($request->getUri());
