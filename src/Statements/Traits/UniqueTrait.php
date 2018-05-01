<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define unique properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait UniqueTrait
{
	/**
	 * Indicate whether the value must be unique
	 * @var \bool
	 */
	protected $_unique;

	/**
	 * Indicate whether the value must be unique
	 * @return \bool
	 */
	public function get_unique()
	{
		return $this->_unique;
	}

	/**
	 * Set whether the value must be unique
	 * @param \bool $unique
	 */
	public abstract function unique($unique = true);

	/**
	 * Set whether the value must be unique
	 * @param \bool $unique
	 */
	protected function set_unique($unique = true)
	{
		$this->_unique = $unique;
		return $this;
	}

	protected function new_query_unique()
	{
		$this->_unique = false;
	}

	protected function parse_query_unique()
	{
		return $this->_unique ? ' UNIQUE' : '';
	}
}
