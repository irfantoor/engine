<?php

# php -S localhost:8000 exception_handler.php

require 'autoload.php';

$ie = new IrfanTOOR\Engine([
	'debug' => [
		'level' => 2
	],
]);

$ie->addHandler(function ($request) use ($ie){
	throw new Exception("Its a test!");
});

$ie->run();
