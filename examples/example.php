<?php

require dirname(__DIR__) . '/vendor/autoload.php';
// require '../src/Autoloader.php';

use IrfanTOOR\Engine\Autoloader;
use IrfanTOOR\Engine;
use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Debug;
use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Middleware;
use IrfanTOOR\Engine\Router;
use IrfanTOOR\Engine\Http\Cookie;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Message;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Engine\Http\Stream;
use IrfanTOOR\Engine\Http\UploadedFile;
use IrfanTOOR\Engine\Http\Uri;

// $loader = new Autoloader();
// $loader->register();
// $loader->addNamespace('IrfanTOOR\Engine', '../src/');
// $loader->addNamespace('IrfanTOOR', '../src/');
// $loader->addNamespace('IrfanTOOR', '../vendor/irfantoor/collection/src/');
// $loader->addNamespace('IrfanTOOR', '../vendor/irfantoor/console/src/');
// $loader->addNamespace('IrfanTOOR', '../vendor/irfantoor/container/src/');
// $loader->addNamespace('JakubOnderka\PhpConsoleColor', '../vendor/jakub-onderka/php-console-color/src/JakubOnderka/PhpConsoleColor/');
// $loader->addNamespace('GuzzleHttp\Stream', '../vendor/guzzlehttp/streams/src/');
// $loader->addNamespace('Psr\Container', '../vendor/psr/container/src/');


define('HACKER_MODE', true);



// $router = new Router();
// Debug::dump($router);
// exit;

// $r = RequestFactory::createFromEnvironment();
// # $r = new Response();
// $uri = $r->getUri();
//
// //*/
// class Hello extends Middleware
// {
//     protected $method;
//     public function __construct($method)
//     {
//         $this->method = $method;
//     }
//
//     public function __invoke(Request $request, Response $response, $next = null)
//     {
//         $request = $request->withMethod($this->method);
//
//         if ($next)
//             list($request, $response) = $next($request, $response);
//
//         return [$request, $response];
//     }
// }

// class Auth extends Middleware
// {
//     protected $id;
//
//     public function __construct($id)
//     {
//         $this->id = $id;
//     }
//
//     public function __invoke(Request $request, Response $response, $next = null)
//     {
//         if (md5($this->id) !== '24b90bc48a67ac676228385a7c71a119') {
//             $body = $response->getBody();
//             $body->write('[' . $this->id . ': Can not be authenticated]');
//
//             $response->send();
//             exit;
//         }
//
//         if ($next)
//             list($request, $response) = $next($request, $response);
//
//         return [$request, $response];
//     }
// }

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
    ]
]);

# $ie->add($ie);
# $ie->add(new hello());

$ie->addRoute('GET', '.*', function($request, $response){
    $stream = $response->getBody();
    $stream->write($request->getMethod() . ': Hello World!');
    return $response;
});

$ie->addRoute('SMART', '.*', 'controller');

$ie->addRoute('ANY', '.*', function($request, $response){
    $stream = $response->getBody();
    $stream->write('default : ' . $request->getMethod());
    return $response;
});

$ie->run();
