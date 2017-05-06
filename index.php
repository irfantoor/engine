<?php

require "vendor/autoload.php";

$ie = new IrfanTOOR\Engine\IE(["debug"=>3]);

$ie->addRoute("GET", ".*", function() use($ie){
	echo "Hello World!";
	# throw new Exception("Its a test exception");
});

# hello();

$ie->run();
