<?php

require 'autoload.php';

use IrfanTOOR\Engine\Exception;

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
