<?php

# php -S localhost:8000 hello-world.php

require ("autoload.php"); # give the path/to/vendor/autoload.php

use IrfanTOOR\Engine;

$ie = new Engine(
    [
        'debug' => [
            'level' => 2
        ],
        'default' => [
            'name' => 'world',
        ]
    ]
);

# name passed as get variable: http://localhost:8000/?name=alfa
# check: http://localhost:8000/?name=alfa&debug=1
# check: http://localhost:8000/?name=alfa&exception=1

# or posted through a form
$ie->addHandler(function ($request) use($ie) {
	$name = $request->getQueryParams()['name'] ?? $ie->config('default.name');

	$response = $ie->create('Response');
    $response->getBody()->write('Hello ' . ucfirst($name) . '!');
    
    if ($request->getQueryParams()['exception'] ?? null) {
        throw new Exception("An exception at your service!");
    }

    if ($request->getQueryParams()['debug'] ?? null) {
        # dump
        d($request);
        d($response);

        # dump and die!
        dd($ie);
    }
    
    # a response must be sent back in normal circumstances!
    return $response;
});

$ie->run();
