<?php

require 'autoload.php';

use IrfanTOOR\Debug;
use IrfanTOOR\Engine\Exception;

Debug::enable(1);
Exception::log();

throw new Exception('Hello World!', 0, true);
