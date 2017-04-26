<?php

namespace IrfanTOOR\Engine;

/**
 * Container with Case Insensitive Identifiers, but keeps track of the Case of the Identifier
 */
class ContainerCI extends Container
{
    protected $keys;
    
    public function __construct($init = []) 
    {
    	parent::__construct($init);
    	foreach($this->data as $id => $v) {
    		$this->keys[strtolower($id)] = $id;
    	}
    }
    
	public function set($id, $value)
	{
		$key = strtolower($id);
		
		if (isset($this->keys[$key]))
			unset($this->data[$this->keys[$key]]);
		else
			$this->keys[$key] = $id;
			
		$this->data[$id] = $value;	
	}
	
	public function remove($id) {
		$key = strtolower($id);
		if (isset($this->keys[$key])) {
			unset($this->data[$this->keys[$key]]);
			unset($this->keys[$key]);
		}
	}
    
    public function get($id, $default=null)
    {
    	$key = strtolower($id);
    	return isset($this->keys[$key]) ? $this->data[$this->keys[$key]] : $default;
    }

    public function has($id){
    	return isset($this->keys[strtolower($id)]) ? true : false;		
    }
}
