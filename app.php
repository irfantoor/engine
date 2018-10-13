<?php

foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

unset($file);

use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine;

$config = [
    # Debug level
    'debug' => [
        'level' => 1,
    ],
    
    # Wether the exceptions be logged
    'exception' => [
        'log' => [
            'enabled' => false,
            'file'    => '/dev/null',
        ],
    ],
];

class App extends IrfanTOOR\Engine
{
    function process($request, $response, $args)
    {
        $response->write('Hello World!');
        # throw new Exception($response->getBody());
        
        return $response;
    }
}

$app = new App($config);
$app->run();    
