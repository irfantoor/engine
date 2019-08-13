<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\Uri;
use IrfanTOOR\Debug;

Debug::enable(1);

$uri  = new Uri(
    'http://irfan:test@www.example.com:8000/hello/world?hello=world#top'
);

d($uri);
d((string) $uri);
$uri = $uri->withPort(80);
d((string) $uri);

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

d((string) $uri);
