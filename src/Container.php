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
		$this->raw = [];

		# Initialize Container
		foreach ($init as $key => $value) {
			$this->raw[$key] = $value;
		}
	}

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws IdNotStringException  Identity id, was not a string.
     * @throws NotFoundException  No entry was found for this identifier: id.
     * @throws ContainerException Error while retrieving the entry referred by identifier: id.
     *
     * @return mixed Entry.
     */
    public function get($id) {
    	if (!is_string($id))
    		throw new IdNotStringException("Identity {$id}, was not a string");

    	if (!$this->has($id))
    		throw new NotFoundException("No entry was found for this identifier: {$id}");

    	try {
    		$result = $this->raw[$id];
    		return $result;
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
}
