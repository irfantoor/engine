<?php

require 'vendor/autoload.php';

use DeepCopy\DeepCopy;
use IrfanTOOR\Engine;
use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Debug;
use IrfanTOOR\Engine\Router;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Headers;
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

class A extends Response
{
    function __construct()
    {
        parent::__construct();
        # $this->headers = new Headers(['hello' => 'world']);
        $this->get('headers')->set(['hello'=>'world!']);
    }

    function withHello($salutation)
    {
        $copier = new DeepCopy();
        $clone = $copier->copy($this);
        #$clone = clone $this;

        # $clone->set('headers', new Headers($this->get('headers')->toArray()));
        $clone->get('headers')->set(['hello'=>'again!']);

        return $clone;
    }
}

$a = new A();
$b = $a->withHello('again');
#$b->set('name', 'B');


Debug::dump(assert($a != $b) ? 'T' : 'F');
Debug::dump(assert($a !== $b) ? 'T' : 'F');


#$b->set('hello', 'world');

//
// Debug::dump(assert($h1 == $h2) ? 'T' : 'F');
// Debug::dump(assert($h1 == $h2) ? 'T' : 'F');
//
// Debug::dump($h1);
// Debug::dump($h2);
//


Debug::dump([$a, $b]);
