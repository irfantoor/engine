<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine;
use IrfanTOOR\Debug;

define('HACKER_MODE', true);

class Controller
{
    protected $ie;

    function __construct($ie)
    {
        $this->ie = $ie;
    }

    function color($txt, $color)
    {
        return '<span style="color:' . $color . '">' . $txt . '</span>';
    }

    function pre($txt)
    {
        return '<pre>' . $txt . '</pre>';
    }

    function defaultMethod($request, $response, $args)
    {
        $c = new IrfanTOOR\Console();
        
        ob_start();
        $c->write($request->getMethod(), 'blue');
        $c->write(' : ', 'yellow');
        $c->writeln('Hello World!', 'red');
        $contents = ob_get_clean();

        $response->write($contents . print_r($args, 1));

        return $response->withStatus(666, 'Tah Dah!');
    }
}

$ie = new Engine([
    'debug' => [
        'level' => 2,
    ],
    
    ''
]);


$request = $ie->ServerRequest();
$response = $ie->Response();
$method = $request->getMethod();

switch($method) {
    case 'GET':
        $response->write($method . ': Hello World!');
        break;
    
    case 'SMART':
        $c = new Controller($ie);
        $args = explode('/',$request->getUri()->getBasePath());
        $response = $c->defaultMethod($request, $response, $args);
        break;
        
    default:
        $response->write($method . ': default');
        break;
}

$response->send();

