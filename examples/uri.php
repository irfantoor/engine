<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Engine\Debug;

Debug::enable(1);

$uri  = new Uri(
    'http://irfan:test@www.example.com:8000/hello/world?hello=world#top'
);

Debug::dump($uri);
Debug::dump((string) $uri);
$uri = $uri->withPort(80);
Debug::dump((string) $uri);

define('HACKER_MODE', true);
$uri = $uri
        ->withUserInfo('neo', '31337-#4k3r')
        ->withHost('MaTrIx.CoM')
        ->withScheme('reality')
        ->withPath('red/pill')
        ->withPort(01101001+01110100)
        ->withQuery('mode=enter')
        ->withFragment('no-panic')
        ;

Debug::dump((string) $uri);
