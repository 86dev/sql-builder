<?php

namespace SQLBuilder\Statements\Traits;
use SQLBuilder\Enums\IndexAlgorithm;

/**
 * Define algorithm properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait AlgorithmTrait
{
	/**
	 * Index algorithm
	 * @var \string
	 */
	protected $_algorithm;

	/**
	 * Get index algorithm
	 * @return \string
	 */
	public function get_algorithm()
	{
		return $this->_algorithm;
	}

	/**
	 * Set index algorithm, see IndexAlgorithm for available values
	 * @param \string $algorithm
	 */
	public abstract function algorithm($algorithm);

	/**
	 * Set index algorithm, see IndexAlgorithm for available values
	 * @param \string $algorithm
	 */
	protected function set_algorithm($algorithm)
	{
		if (!empty($algorithm) && !IndexAlgorithm::isValidValue($algorithm))
			throw new \UnexpectedValueException("Index {$this->_default_name()}: invalid algorithm ('$algorithm'). Expected values are ".implode(', ', array_map([$this, '_quote'], IndexAlgorithm::values()))." or empty.");
		$this->_algorithm = $algorithm;
		return $this;
	}

	protected function new_query_algorithm()
	{
		$this->_algorithm = null;
	}

	protected function parse_query_algorithm()
	{
		return $this->_algorithm ? "ALGORITHM = $this->_algorithm" : '';
	}

}
