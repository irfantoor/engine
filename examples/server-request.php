<?php

require 'autoload.php';

use IrfanTOOR\Engine\Http\Cookie;
use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Debug;

Debug::enable(3);


$sr = new ServerRequest();

d($sr);
d($sr->getCookieParams());
