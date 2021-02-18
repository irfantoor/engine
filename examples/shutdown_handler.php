<?php

# php -S localhost:8000 exception_handler.php

require 'autoload.php';

use IrfanTOOR\Debug;

$ie = new IrfanTOOR\Engine([
    'debug' => [
        'level' => 2
    ],
    'environment' => [
        'HELLO' => 'World!',
        'HTTP_HOST' => 'localghost',
    ],
    'site' => [
        'name' => 'My Site',
        'root' => 'http://mysite.com',
        # ...
    ]
]);

$ie->addHandler(function ($request) use ($ie){
    dd($ie);
});

$ie->run();
