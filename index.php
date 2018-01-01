<?php

require 'vendor/autoload.php';

use DeepCopy\DeepCopy;
use IrfanTOOR\Engine;
use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Debug;
use IrfanTOOR\Engine\Router;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Message;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\Response;

Debug::enable(1);
// $router = new Router();
// Debug::dump($router);
// exit;

// $r = Request::createFromEnvironment();
// # $r = new Response();
// $uri = $r['uri'];
// $uri = $uri->with('userinfo', ['irfan', 'toor'])
//         ->with('path', 'hello/world one');
//
// Debug::dump( (string) $uri);
//
// exit;


// $e = new Engine([
//     'debug' => [
//         'level' => 1,
//     ]
// ]);
//
// $e->addRoute('GET', '.*', function($request, $response){
//     $response->set('body', 'Hello World!' . $request->get('method'));
//     return $response;
// });
//
// $e->run();
#print_r($e->container()['environment']);


# Debug::dump($e);


$a = new Message('1.0', ['hello' => 'world!']);
$b = $a->withHeader('hellO', 'again');
$c = $b->withProtocolVersion('1.1');
$c->getBody()->write('wake up neo!');

Debug::dump(assert($a != $b) ? 'T' : 'F');
Debug::dump(assert($a !== $b) ? 'T' : 'F');
Debug::dump($a->getHeaders());
Debug::dump($c->toArray());

// $req = Request::createFromEnvironment();
// Debug::dump($req);
