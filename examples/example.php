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

        $stream = $response->getBody();
        $stream->write($contents . print_r($args, 1));

        return $response->withStatus(666, 'Tah Dah!');
    }
}

$ie = new Engine([
    'debug' => [
        'level' => 2,
    ],
]);

$ie->addRoute('GET', '/', function($request, $response){
    $stream = $response->get('body');
    $stream->write($request->get('method') . ': Hello World!');
    return $response;
});

$ie->addRoute('SMART', '/', 'controller');

$ie->addRoute('ANY', '.*', function($request, $response){
    $stream = $response->get('body');
    $stream->write($request->get('method') . ':default' );
    return $response;
});

$ie->run();
