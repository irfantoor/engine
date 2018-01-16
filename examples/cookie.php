<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine\Http\Cookie;
use IrfanTOOR\Engine\Debug;

Debug::enable(1);

$cookies = Cookie::createFromArray([
    'Hello' => 'World!',
    'locked' => 'again',
]);


Debug::dump($cookies[0]->getValue());
$cookies[2] = $cookies[0]->withValue(null);


Debug::dump($cookies);
