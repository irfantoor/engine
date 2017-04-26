<?php

namespace IrfanTOOR\Engine;

class Controller
{	
	function __construct() {}
		
	function __call($func, array $args)
	{
		return $this->default_method($args);
	}
	
	function default_method() 
	{
		throw new Exception('default_method must be defined in your controller');
	}
}
