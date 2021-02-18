Irfan's Engine
==============

A bare-minimum PHP framework, with the spirit with which the HTTP was invented.
focussing on the requests and the responses. A Swiss-knife for world-wide-web.

The objective of this library is to be a Bare-minimum, Embeddable and Educative.

Irfan's Engine uses IrfanTOOR\Http which implements the psr/http-message.

Note: This documentation is just to get you started, you are encouraged to study
the code and the examples in the examples folder, which might help you get going
, by adding, extending or even writing your own classes and/or frameworks.

## Quick Start

### 1. Installation

Install the latest version with

```sh
composer require irfantoor/engine
```

Note: Irfan's Engine requires PHP 7.0 or newer.

## Usage

You can find the code in examples folder.

### hello-world.php
```php
<?php

# php -S localhost:8000 hello-world.php

require ("autoload.php"); # give the path/to/vendor/autoload.php

use IrfanTOOR\Engine;

$ie = new Engine(
    [
        'debug' => [
            'level' => 2
        ],
        'default' => [
            'name' => 'world',
        ]
    ]
);

# name passed as get variable: http://localhost:8000/?name=alfa
# check: http://localhost:8000/?name=alfa&debug=1
# check: http://localhost:8000/?name=alfa&exception=1

# or posted through a form
$ie->addHandler(function ($request) use($ie) {
	$name = $request->getQueryParams()['name'] ?? $ie->config('default.name');

	$response = $ie->create('Response');
    $response->getBody()->write('Hello ' . ucfirst($name) . '!');
    
    if ($request->getQueryParams()['exception'] ?? null) {
        throw new Exception("An exception at your service!");
    }

    if ($request->getQueryParams()['debug'] ?? null) {
        # dump
        d($request);
        d($response);

        # dump and die!
        dd($ie);
    }
    
    # a response must be sent back in normal circumstances!
    return $response;
});

$ie->run();
```

### Provider of Http Suite

Irfan's Engine uses IrfanTOOR\Http suite by default. You can use another provider
by defining it through passed config.

Download the Psr compliant Http suite you want to use and define the provider in
the config, for example you can use slim\psr7;

```sh
$ composer require slim/psr7
```

```php
<?php

require 'path/to/autoload.php';

use IrfanTOOR\Engine;

# Create engine
$ie = new Engine(
	[
		'http' => [
			'provider' => 'Slim\\Psr7',
		]
	]
);

# Add handler
$ie->addHandler(function($request) use($ie) {
	# Request received by handle will be a Slim\Psr7 Request

	# Psr\Slim7 Response
	$response = $ie->create('Response');
	$respone->write("Hello world from Slim\Psr7");
	return $response;
});

# Run ...
$ie->run();
```

### Environment

Environment instance contains the environment variables and the headers passed,
by the web server, which are automatically converted to headers and added to the
request class.

Environment can be mocked by defining the 'env' element in the configuration file,
or as follows, if using without the engine:

### Uri
Whenever a server request is created, a Uri containing the parsed information of the
requested url is also present and can be accessed as:

```php
class RequestHandler
{
	protected $engine;

	function __construct($engine)
	{
		$this->engine = $engine;
	}

	function handle(RequestInterface $request): ResponseInterface
	{
		$uri =  $request->getUri();
		$host = $uri->getHost();
		$port = $uri->getPort();
		$path = $uri->getPath();
		# ...

		$response = $this->engine->create('Response');
		# ...

		return $response;
	}
}

$ie = new Engine();
$ie->addHandler(new RequestHandler($ie));
$ie->run();
```

### Headers

```php
# ...
# Setting a header
$response = $response
	->withHeader('Content-Type', 'text/plain')
	->withHeader('keywords', 'hello, world')
;

# Removing a header
$response = $response->withoutHeader('unwanted-header');

# Checking a header
if ($response->hasHeader('content-type')) {
	# Do something ...
}

# Getting a header, note that the key of headers is not case sensitive
$content_type = $response->getHeader('CONTENT-type');  
# ...
```

### Creating your config file: path/to/config.php

Create a config.php file:

```php
<?php

return [
	'debug' => [
		'level' => 0, # Or can be 1, 2 or 3
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
$ie = new IrfanTOOR\Engine($config));

# OR preferably:
$ie = new IrfanTOOR\Engine([
	'config_file' => "path/to/config.php",
]);

$ie->config('site.name'); # Returns "mysite.com"
```

### Debugging

You can enable debugging while coding your application, a short, concise and to
the point, error description and trace is dumped in case of any exception. You
can enable the debugging using config if using Irfan's Engine.

```php
<?php
require "path/to/vendor/autoload.php";

use IrfanTOOR\Engine;
$ie = new Engine(
	[
		'debug' => [
			'level'  => 2, # Can be from 0 to 3
		]
	]
);
# ...
# If debug level is above 0, you can use the function d() && dd() to dump a 
# variable while development.
d($request);
dd($request->getHeaders());
```

## About

**Requirements**
Irfan's Engine works with PHP 7.0 or above.

**License**

Irfan's Engine is licensed under the MIT License - see the `LICENSE` file for details.
