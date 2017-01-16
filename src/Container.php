<?php

namespace IrfanTOOR\Engine;

use Pimple\Container as PimpleContainer;

class Container extends PimpleContainer
{
	function __construct($init=[]) {
		parent::__construct($init);	
	}

	# you can set a key using this notation: $c->hello = 'world'
	function __set($key, $value) {
		$this->set($key, $value);
	}
	
	# you can get a key using this notation: $h = $c->hello
	# @return Mixed - the value of the key is returned
	function __get($key) {
		$this->get($key);
	}
	
	# checks if a key exists $c->has('hello')
	# @return Boolean
	public function has($key) {
		return $this->offsetExists($key);
	}
	
	# you can get the value of key, or the default value if does not exist
	public function get($key, $default = null)
	{
		if ($this->has($key))
			return $this->offsetGet($key);
		
		return $default;
	}
	
	# you can set the value of key to value or an array of assignments
	public function set($key, $value=null) {	
		if (is_array($key)) {
			foreach($key as $k => $v) {
				$this->set($k, $v);
			}
		} else {
			$this->offsetSet($key, $value);
		}
	}
	
	# clear a key
	public function clear($key) {
		$this->offsetUnset($key);
	}
}
