<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\Cookie;
use IrfanTOOR\Debug;

Debug::enable(3);

$cookies = [
    # adds/modifies the value of a cookie key
    new Cookie(
        [
            'name' => 'Hello',
            'value' => 'World!',
            'domain' => 'localhost',
            'secure' => false,
            'httponly' => true,
        ]
    ),

    # deletes a cookie key
    new Cookie(
        [
            'name' => 'locked',
            'value' => null,
            'domain' => 'localhost',
            'secure' => false,
            'httponly' => true,
        ]
    ),    
];

foreach ($cookies as $c) {
    $c->send();
}

echo '<pre>';
print_r($_SERVER);
