<?php

namespace IrfanTOOR\Engine;

class Container
{
    protected $data=[];
    
    /**
     * Constructs the container with an intial data array
     *
     */
    public function __construct($init = null) 
    {
    	if (is_array($init))
	    	$this->data = $init;
    }
    
    /**
     * Sets an entity in the container
     *
     * @param $id		string	Identifier
     * @param $value	mixed	Value
     */
	public function set($id, $value)
	{
		$this->data[$id] = $value;
	}
	
	/**
	 * Removes the entity from the container 
	 *
	 * @param $id	String		Identifier
	 */
	public function remove($id) {
		if ($this->has($id))
			unset($this->data[$id]);
	}
    
    /**
     * Finds an entry of the container by its identifier and returns it or returns the default
     *
     * @param string $id        Identifier of the entry to look for.
     * @param mixed  $default   Default value in case the No Entry is found for the identifier
     *                          in the container
     *
     * @return mixed Entry.
     */
    public function get($id, $default=null)
    {
    	return $this->has($id) ? $this->data[$id] : $default;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id){
    	return array_key_exists($id, $this->data);
    }
    
    /**
     * Returns the container as array
     *
     * returns Array
     */
    public function raw() 
    {
    	return $this->data;
    }
}
