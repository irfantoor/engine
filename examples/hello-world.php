<?php

# php -S localhost:8000 hello-world.php
define('ROOT', dirname(__DIR__) . "/");

require (ROOT . "vendor/autoload.php"); # give the path/to/vendor/autoload.php

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

$txt = <<<END
<div>
<a href="/">home</a> |
<a href="/?debug=true">debug</a> |
<a href="/?exception=true">exception</a> |
<a href="/?name=irfan">hello irfan</a>
</div>
END;
    $response = $ie->create('Response');

    if ($request->getQueryParams()['exception'] ?? null) {
        throw new Exception("An exception at your service!");
    } elseif ($request->getQueryParams()['debug'] ?? null) {
        # dump
        d($request);
        d($response);

        # dump and die!
        dd($ie);
    } else {
        $response->getBody()->write($txt);
        $response->getBody()->write('Hello ' . ucfirst($name) . '!');
    }

    # a response must be sent back in normal circumstances!
    return $response;
});

$ie->run();
