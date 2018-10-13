<?php

require 'autoload.php';

$ie = new IrfanTOOR\Engine([
	'debug' => [
		'level' => 2
	],
	'environment' => [
		'HELLO' => 'World!',
		'HTTP_HOST' => 'localghost',
	],
	'site' => [
		'name' => 'My Site',
		'root' => 'http://mysite.com',
		# ...
	]
]);

ob_start();
IrfanTOOR\Debug::dump($ie);
$contents = ob_get_clean();

$response = $ie->Response();
$response->write($contents);
$response->write('Debug Level: ' . $ie->config('debug.level'));
$response->write(', Site: ' . $ie->config('site.name') . '<' . $ie->config('site.root') . '>');
$response->send();
