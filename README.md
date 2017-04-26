Irfan's Engine v0.42
====================

A bare-minimum PHP framework, with the spirit with which the HTTP was invented.
focussing on the requests and the responses. A swiss-knife for world-wide-web.
	
The objective of this library is to be a Bare-minimum, Embeddable and Educative


Installation
------------

Install the latest version with

```sh
composer require irfantoor/engine
```

Requires PHP 5.4 or newer.

Basic Usage
-----------

Here's a basic usage example:

```php
<?php

require '/path/to/vendor/autoload.php';

use IrfanTOOR\Engine\IE;

$ie = new IE();

# request filters
$ie->addRoute('ANY', '.*', function (){ echo "<h1>Hello World!</h1>"; });

$ie->run();
```

### Defining routes

```php
<?php

$ie = new IE($config);

$ie->addRoute('GET', '/', function (){ echo "<h1>Hello World!</h1>"; });		# closure
$ie->addRoute('GET|POST', 'help(/.*)?', 'index@App\Controllers\MyController');	# index

# default 404
# $ie->addRoute('ANY', '.*', function() use($ie){
# 	$ie->response["status"] = 404;
# 	$ie->response["body"] = ["Error" => "404 - Page Not Found"];
# });

$ie->addRoute('ANY', '.*', 'App\Controllers\MyController'); 					# default_method

$ie->run();
```


### Creating Controllers

A Basic Controller: app/controllers/MyController.php

```php
<?php

namespace App\Controllers;

use IrfanTOOR\Engine\Controller;
use IrfanTOOR\Engine\View;

class MyController extends Controller
{
	public function index() {
		# $ie = IE::getInstance();
		
		$v = new View("home");
		$v->show();
	}
	
	public function default_method() {
		return ["you are" => "wondering somewhere!"];
	}
}
```

the index can be accessed using:

```php

$ie = new IrfanTOOR\Engine\IE(["debug" => 1]);

$ie->addRoute("GET|POST", "/", "index@App\Controllers\MyController");

$ie->run();
```

### Creating Views

A basic view: app/views/home.php

```html
<html>
<body>
	<h1>Hello World!</h1>
</body>
</html>
```

### Creating your config file: path/to/config.php

```
<?php

return [
	'debug' => 3, # 0, 1, 2 or 3
	'env' 	=> [
			
	],
];
```

and then this config can be included like this:

```php
<?php

$config = require("path/to/config.php");
$ie = new IE($config);
...
```


About
-----

**Requirements**
Irfan's Engine works with PHP 5.6 or above.
 
**License**

Irfan's Engine is licensed under the MIT License - see the `LICENSE` file for details
