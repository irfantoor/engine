<?php

namespace IrfanTOOR\Engine;

use Interop\Container\ContainerInterface;
use IrfanTOOR\Engine\Exception\ContainerException;
use IrfanTOOR\Engine\Exception\IdNotStringException;
use IrfanTOOR\Engine\Exception\NotFoundException;

class Container implements ContainerInterface
{
	protected $raw;

	/**
	 * Constructs a container and initializes with the initial data
	 * 
	 * @param array $init contains a key, value array, so that the container be initialized
	 */
	function __construct($init=[]) {
        $this->factories = [];
		$this->raw = [];

		# Initialize Container
		$this->set($init);
	}

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     * @param mixed $args used as arguments to be passed to a closure/factory function
     *
     * @throws IdNotStringException  Identity id, was not a string.
     * @throws NotFoundException  No entry was found for this identifier: id.
     * @throws ContainerException Error while retrieving the entry referred by identifier: id.
     *
     * @return mixed Entry.
     */
    public function get($id, $args=null) {
    	if (!is_string($id))
    		throw new IdNotStringException("Identity {$id}, was not a string");

    	if (!$this->has($id))
    		throw new NotFoundException("No entry was found for this identifier: {$id}");

    	try {
            if (array_key_exists($id, $this->factories))
                return $this->raw[$id]($args);
            else
                return $this->raw[$id];
    	} catch(\Exception $e) {
			throw new ContainerException("Error while retrieving the entry referred by identifier: {$id}");
    	}
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id) {
    	if (!is_string($id))
    		return false;

    	if (array_key_exists($id, $this->raw))
    		return true;

    	return false;
    }

    /**
     * Sets a entry for a given identifier: id
     *
     * @param string $id Identifier of the entry or an array of key, value pairs
     * @param string $id Identifier of the entry.
     *
     * @throws IdNotStringException  Identity id, was not a string.
     */
    public function set($id=null, $entry=null) {
        if (is_array($id)) {
            foreach ($id as $k => $v) {
                $this->set($k, $v);
            }
        }
        else {
            if (!is_string($id))
                throw new IdNotStringException("Identity {$id}, was not a string");

            if (!is_string($entry) && is_callable($entry)) {
                $this->factories[$id] = true;
            }

            $this->raw[$id] = $entry;
        }
    }

    /**
     * Removes an entry for the given identifier.
     *
     * @param string $id Identifier of the entry to look for.
     */
    public function remove($id) {
        if (!is_string($id))
            throw new IdNotStringException("Identity {$id}, was not a string");

        if ($this->has($id)) {
            $this->set($id, null);
            unset($this->factories[$id]);
            unset($this->raw[$id]);
        }
    }
}
