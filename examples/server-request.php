<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\Cookie;
use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Debug;

Debug::enable(1);


$sr = (new ServerRequest())
    ->withHeader('User-Agent', 'Hello World v1.0')
    ->withCookieParams([
        'hello'  => 'world!',
        'summer' => 'sort'
    ]);

Debug::dump($sr);
Debug::dump($sr->getCookieParams());
