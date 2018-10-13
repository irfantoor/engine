<?php

# requires autoload file
foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}
unset($file);

# define your app class
class App extends IrfanTOOR\Engine
{
    function process($request, $response, $args)
    {
        $response->write('Hello World!');
        # throw new Exception($response->getBody());
        
        return $response;
    }
}

# config
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

$app = new App($config);
$app->run();    