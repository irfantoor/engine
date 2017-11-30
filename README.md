Irfan's Engine
==============

A bare-minimum PHP framework, with the spirit with which the HTTP was invented.
focussing on the requests and the responses. A Swiss-knife for world-wide-web.

The objective of this library is to be a Bare-minimum, Embeddable and Educative.

Note: This documentation is just to get you started, you are encouraged to study the
code as well, which might help you get going, by adding, extending or even writing
your own classes and/or frameworks.

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

$res = new IrfanTOOR\Response(200, 'Hello World!');
$res->send();
```

```php
<?php
...
$res = new IrfanTOOR\Response();

$res->set('body', 'Hello World!');
# $res['body'] = 'Hello World!';

$res->send();
```

### Hello World! - using parameters from Request

```php
<?php
...
$req = new IrfanTOOR\Request();
$res = new IrfanTOOR\Response();

# name passed as http://example.com/?name=alfa
$name = $req['get']['name'] ?: 'World!';

$res->set('body', 'Hello ' . $name);
# $res['body'] = 'Hello World!';

$res->send();
```

### Using Engine and routes

```php
<?php
...
$ie = new IrfanTOOR\Engine();

# GET method => http://example.com/...
$ie->addRoute('GET', '/', function ($req, $res){
		$res['body'] = 'Welcome Home!';
		return $res;
});

# name passed as http://example.com/hello/?name=alfa
$ie->addRoute('GET', 'hello', function ($req, $res){
		$name = $req['get']['name'] ?: 'World!';

		$res->set('body', 'Hello ' . $name);
		# $res['body'] = 'Hello ' . $name;

		return $res;
});


# ANY allowed method => http://example.com/...
$ie->addRoute('ANY', '.*', function ($req, $res){
		$res['status'] = 404;
		return $res;
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
	$e = new IrfanTOOR\Environment([
		'HTTP_HOST' => 'example.com',
		'Engine' => 'My Engine v1.0',
	]);
	...

	# Its a locked singleton collection
	# which can be accessed using static function getInstance
	$e = IrfanTOOR\Environment::getInstance();

	$host = $e['HTTP_HOST']; # Environment is a case sensitive collection
```

### Uri

Whenever a request is created, a Uri containing the parsed information of the requested url
is also present and can be accessed as:

```php
	$req = new Request();
	# $ie->addRoute('ANY', '.*', function ($req, $res){ ...

	$host = $req['uri']['host'];
 	port = $req['uri']['port'];
```

### Headers

```php
<?php
...
		# Setting a header
		$response['headers']['Content-Type'] = 'text/plain';
		$response['headers']->set('Content-Type', 'text/json');

		# Removing a header
		$response['headers']->remove('unwanted-header');

		# checking a header
		if ($response['headers']->has('content-type')) {
			# do something ...
		}

		# getting a header
		$content_type = $response['headers']['CONTENT-type']; # the key of headers is case insensitive
...
```

###  Router Usage

```php
<?php
...

The router can be independently initialized and used
$router = new Router(['GET', 'POST']); # Only GET and POST methods are allowed

$router->add('GET', '/',     'home');
$router->add('GET', 'hello', 'hello');
$router->add('ANY', '.*',    'default');

$result = $r->process('GET', 'http://example.com/?hello=world');
switch($result['callable']) {
		case 'home':
			...
		case 'hello':
			...
		default:
			...
}
```

###  Router usage with the Engine

```php
<?php
...
$ie = new IE();
$router = $ie->getRouter();
$router->setAllowedMethods(['GET', 'POST']);
$router->add('GET', '/', 'home');
...
$ie->setRouter($router);
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
	'env' 	=> [
		'REMOTE_ADDR' => '192.168.1.1',
		'HELLO' => 'WORLD',
	],
	'sitename' => 'MySite.com',
];
```

and then this config can be included like this:

```php
<?php
$config = require("path/to/config.php");
$ie = new IE($config);

$ie->addRoute('GET', '/', function($request, $response) use($ie){
		$response['body'] = 'Welcome to ' . $ie->config('sitename') . '!';
		return $response;
});

$ie->run();
...
```

### Debugging

You can enable debugging while coding your application, a short, consise and to the point,
error description and trace is dumped in case of any exception. You can enable the debugging
using config if using Irfan's Engine or by simply by using this class in any of your code directly as:

```php
<?php
	require "path/to/vendor/autoload.php";
	use IrfanTOOR\Debug;
	Debug::enable(2); # 2 is debug level

	...
	# Not only will it help debugging the errors but you can use it to dump data etc.

	Debug::dump($request);
	Debug::dump($response['headers']);
```

Debug is single file without any dependency but try using it in the starting index.php or
bootstrap.php file so that it can detect any errors in the succeeding files.


## About

**Requirements**
Irfan's Engine works with PHP 7.0 or above.

**License**

Irfan's Engine is licensed under the MIT License - see the `LICENSE` file for details
