<?php

# php -S localhost:8000 example.php

require 'autoload.php';

use IrfanTOOR\Debug;

$ie = new IrfanTOOR\Engine([
    'debug' => [
        'level' => 2
    ],
]);

$ie->addHandler(function ($request) use ($ie){
    $response = $ie->create('Response');
    $key = $request->getQueryParams()['key'] ?? null;

    if ($key)
        $result = ['hash' => md5($key)];
    else
        $result = ['error' => 'key not provided as $_GET variable'];

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'text/json');
});

$ie->run();
