<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define collate properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait CollateTrait
{
	/**
	 * Collate
	 * @var \string
	 */
	protected $_collate;

	/**
	 * Get collate
	 * @return \string
	 */
	public function get_collate()
	{
		return $this->_collate;
	}

	/**
	 * Set collate
	 * @param \string $collate
	 */
	public abstract function collate($collate);

	/**
	 * Set collate
	 * @param \string $collate
	 */
	protected function set_collate($collate)
	{
		$this->_collate = $collate;
		return $this;
	}

	protected function new_query_collate()
	{
		$this->_collate = null;
	}

	protected function parse_query_collate()
	{
		return $this->_collate ? "COLLATE $this->_collate" : '';
	}

}
