<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine\Http\Cookie;

use IrfanTOOR\Debug;

Debug::enable(1);

$cookies = Cookie::createFromArray(
    [
        'Hello' => 'World!',
        'locked' => null,
    ],
    [
        'domain'   => 'localhost',
        'secure'   => true,
        'httponly' => true,
    ]
);

foreach($cookies as $c)
    $c->send();

//
//
// Debug::dump($cookies[0]->getValue());
// $cookies[2] = $cookies[0]->withValue(null);
//
//
// Debug::dump($cookies);
//
// $c  = new Cookie('hello');
// $c->send();
//
// print_r($c);


// setcookie('hello', 'world', time() + 24 * 60 * 60);
Debug::dump(
    [
        $c,
        $_COOKIE
    ]
);




# Debug::dump($c);
