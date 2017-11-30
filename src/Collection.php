<?php

namespace IrfanTOOR;

class Collection implements \ArrayAccess
{
	protected
		$locked = false,
		$data   = [];

	/**
	 * Constructs the container
	 *
	 * $init: array of key, value pair to initialize our collection with
	 */
	function __construct($init = [])
	{
        $this->set($init);
	}

	/**
	 * Locks the container - making it readonly
	 */
	function lock()
	{
		$this->locked = true;
	}

	/**
	 * Sets an $identifier and its Value pair
	 * # $id   : identifier or array of id, value pairs
	 * # $value: value of identifier or null if the parameter id is an array
	 */
	function set($id, $value = null)
	{
		if ($this->locked)
            return false;

		if (is_array($id)) {
			foreach ($id as $k => $v) {
				$this->set($k, $v);
			}
		}
		elseif (is_string($id)) {
			$this->setItem($id, $value);
		}
        else {
            return false;
        }
	}
	### Alternative usage
		# helps to set while accessing as array
		function offsetSet($id, $value) { return $this->set($id, $value); }
	###

	/**
	 * Sets an $identifier and its Value pair
	 * # $id   : identifier id
	 * # $value: value of identifier
	 *
	 * It is defined separately to extend the container
	 */
	function setItem($id, $value) {
		$this->data[$id] = $value;
	}

	/**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
	function has($id)
	{
        return (is_string($id)) ? array_key_exists($id, $this->data) : false;
	}
	### Alternative usage
		# helps to check if present, while accessing as array
		function offsetExists($id) { return $this->has($id); }
	###

	/**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     */
	function get($id, $default = null)
	{
        return $this->has($id) ? $this->data[$id] : $default;
	}
	### Alternative usage
		# helps to get while accessing as array
		function offsetGet($id) { return $this->get($id, null); }
	###

	/**
	 * Removes the value from identified by an identifier from the container
	 * @param string $id: identifier
	 *
     * @return boolval true if successful in removing, false otherwise
	 */
	function remove($id)
	{
        if ($this->locked) {
            return false;
        }
        elseif ($this->has($id)) {
            unset($this->data[$id]);
            return true;
        }
        else {
            return false;
        }
	}
	### Alternative usage
		# helps to unset when id is accessed as array
		function offsetUnset($id) { return $this->remove($id); }
	###

	function toArray() {
		return $this->data;
	}
}
