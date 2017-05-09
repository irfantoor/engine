<?php

namespace IrfanTOOR\Engine;

class Controller
{
	protected 
		$ie,
		$config;
	
	function __construct() {
		$this->ie = IE::getInstance();
		$this->config = $this->ie->config->toArray();
	}
	
	function __call($func, array $args)
	{
		return $this->default_method($args);
	}
	
	function default_method() 
	{
		throw new Exception("default_method must be defined in your controller");
	}
	
	function show($view, $data=[]) {
		$v = new View($view);
		$v->show($data);
	}
}
