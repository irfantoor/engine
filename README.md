Irfan's Engine v0.8.5
=====================

A bare-minimum PHP framework, with the spirit with which the HTTP was invented.
focussing on the requests and the responses. A Swiss-knife for world-wide-web.

The objective of this library is to be a Bare-minimum, Embeddable and Educative.

Irfan's Engine now implements the PSR-7 classes and conforms to the validation
constraints imposed. You can break out of these constraints by using:

```php
// You can enable the hacker mode by defining this constant
define('HACKER_MODE', true);
```

If this constant is defined as a non false value, you can avoid all of the
validations, though certain constraints can not be eliminated, which are
essential for the proper functioning of the underlying system.

Note: This documentation is just to get you started, you are encouraged to study
the code and the examples in the examples folder, which might help you get going
, by adding, extending or even writing your own classes and/or frameworks.

## Installation

Install the latest version with

```sh
composer require irfantoor/engine
```

Requires PHP 7.0 or newer.

## Usage

Here are a few examples :

### Hello World! - using response
```php
<?php
require '/path/to/ ... vendor/autoload.php';
use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Engine\Http\Response;

$response = (new Response())
              ->withStatus(Response::STATUS_IM_A_TEAPOT)
              ->write('Hello World!')
...
$response->send();
```

```php
<?php
...
(new Response())
	->withStatus(Response::STATUS_IM_A_TEAPOT)
	->write('Hello World!')
	->send();
```

### Hello World! - using parameters from Request

```php
<?php
...
# name passed as get variable: http://example.com/?name=alfa
# or posted through a form
$name = (new ServerRequest)->getAttribute('name', 'World');
(new Response())
	->write('Hello ' . ucfirst($name) . '!')
	->send();
```

### Using Engine and routes

```php
<?php
...
$ie = new IrfanTOOR\Engine();

# GET method => http://example.com/...
$ie->addRoute('GET', '/', function ($request, $response){
	$response = $response->write('Hello World!');
	return $response;
});

# name passed as http://example.com/hello/?name=alfa
$ie->addRoute('GET', 'hello', function ($request, $response){
	$name = $request->getAttribute('name', 'World');
	return $response->write('Hello ' . ucfirst($name) . '!')
});


# ANY allowed method => http://example.com/...
$ie->addRoute('ANY', '.*', function ($request, $response){
	return $response
		->withStatus(404)
		->write('Error: page not found');
});

$ie->run();
```

### Environment

Environment instance contains the environment variables and the headers passed,
by the web server, which are automatically converted to headers and added to the
request class.

Environment can be mocked by defining the 'env' element in the configuration file,
or as follows, if using without the engine:

```php
<?php
use IrfanTOOR\Engine\Http\Environment;

$e = new Environment([
	'HTTP_HOST' => 'example.com',
	'Engine' => 'My Engine v1.0',
]);

// Environment is a case sensitive collection
$host   = $e->get('HTTP_HOST', 'localhost');
$engine = $e->get('Engine');
```

### Uri

Whenever a server request is created, a Uri containing the parsed information of the
requested url is also present and can be accessed as:

```php
$request  = new ServerRequest();
$response = new Response();
// $ie->addRoute('ANY', '.*', function ($request, $response){ ...

$uri =  $request->getUri();
$host = $uri->getHost();
$port = $uri->getPort();
```

### Headers

```php
<?php
...
# Setting a header
$response->withHeader('Content-Type', 'text/plain');
$response->withHeader('keywords', 'hello, world');

# Removing a header
$response->withoutHeader('unwanted-header');

# checking a header
if ($response->hasHeader('content-type')) {
	# do something ...
}

# getting a header, note that the key of headers is not case sensitive
$content_type = $response->getHeader('CONTENT-type');  
...
```

###  Router Usage

```php
<?php
...
use IrfanTOOR\Engine\Router;

The router can be independently initialized and used
$router = new Router();
$router->setAllowedMethods(['GET', 'POST']);

$router->add('GET', '/',     'Home');
$router->add('GET', 'hello', 'Hello');
$router->add('ANY', '.*',    'Default'); # Note any is not a method but a directive.
...
$result = router->process('GET', 'http://example.com/?hello=world');
switch($result['type']) {
	case 'closure':
		...
	case 'string':
		...
	default:
		...
}
```

###  Router usage with the Engine

```php
<?php

require 'path/to/vendor/autoload.php';

$ie = new IrfanTOOR\Engine([
	'debug' => [
		'level' => 1
	]
]);

$ie->addRoute('GET', '/', function($request, $response){
	$response->write('OK');
	return $response;
});

$ie->run();
```

### Creating your config file: path/to/config.php

```php
<?php

return [
	'debug' => [
		# this is for production
		# 0 -- no Debug::dump($v) is processed

		# these are for the development
		# 1 -- elapsed time is attached to the response
		# 2 -- included files are included in the dump
		# 3 -- detail of routes and the environment is also dumped

		'level' => 0, # or can be 1, 2 or 3
	],
	'environment' 	=> [
		'REMOTE_ADDR' => '192.168.1.1',
		'HELLO' => 'WORLD',
	],
	'site' => [
		'name' => 'mysite.com',
	]
];
```

and then this config can be included like this:

```php
<?php
$config = require("path/to/config.php");
$ie = new IrfanTOOR\Engine($config);

$ie->addRoute('GET', '/', function($request, $response) use($ie){
	$response->write('Welcome to ' . $ie->config('site.name') . '!');
});

$ie->run();
```

### Debugging

You can enable debugging while coding your application, a short, concise and to
the point, error description and trace is dumped in case of any exception. You
can enable the debugging using config if using Irfan's Engine or by simply by
using this class in any of your code directly as:

```php
<?php
require "path/to/vendor/autoload.php";
use IrfanTOOR\Engine\Debug;
Debug::enable(2); # 2 is debug level

...
# You can use it to dump data etc.

Debug::dump($request);
Debug::dump($response->getHeaders());
```

Debug has a dependency on IrfanTOOR\\Console for dumping the results on the
console. Try including it in the starting index.php or bootstrap.php file so
that it can detect any errors in the succeeding files.

## About

**Requirements**
Irfan's Engine works with PHP 7.0 or above.

**License**

Irfan's Engine is licensed under the MIT License - see the `LICENSE` file for details
