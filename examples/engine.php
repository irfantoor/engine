<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$ie = new IrfanTOOR\Engine([
	'debug' => [
		'level' => 1
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

$ie->addRoute('GET', '/', function($request, $response) use($ie){
	$env = $ie->container()['environment'];
	ob_start();
	IrfanTOOR\Engine\Debug::dump($env);
	$contents = ob_get_clean();
	$response->write($contents);
	$response->write('Debug Level: ' . $ie->config('debug.level'));
	$response->write(', Site: ' . $ie->config('site.name') . '<' . $ie->config('site.root') . '>');
	return $response;
});

$ie->run();
