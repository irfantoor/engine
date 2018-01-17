<?php

// require '../vendor/autoload.php';
require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Debug;

Debug::enable(1);

// define('HACKER_MODE', true);

// (new Response())
//     // ->withStatus(777, 'Lucky Seven') # this requires HACKER_MODE
//     ->withHeader('Engine', 'Irfan\'s Engine v0.8')
//     ->write('You are lucky!')
//     ->send();


// $response = (new Response())
// 			->withStatus(Response::STATUS_IM_A_TEAPOT)
// 			->write('Hello World!');
//
// $response->send();

$name = (new ServerRequest)->getAttribute('name', 'World');
(new Response())
	->write('Hello ' . ucfirst($name) . '!')
	->send();
