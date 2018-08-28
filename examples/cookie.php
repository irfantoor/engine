<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\Cookie;
use IrfanTOOR\Debug;

Debug::enable(3);

$cookies = [
    new Cookie(
        [
            'name' => 'Hello',
            'value' => 'World!',
            'domain' => 'localhost',
            'secure' => true,
            'httponly' => true,
        ]
    ),

    new Cookie(
        [
            'name' => 'locked',
            'value' => null,
            'domain' => 'localhost',
            'secure' => true,
            'httponly' => true,
        ]
    ),    
];

foreach ($cookies as $c) {
    $c->send();
}

echo '<pre>';
print_r($_SERVER);
