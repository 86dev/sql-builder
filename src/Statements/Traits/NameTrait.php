<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define name properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait NameTrait
{
	/**
	 * Name
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function get_name()
	{
		return $this->_name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 */
	protected function set_name($name)
	{
		$this->_name = $name;
	}

	/**
	 * Reset name query value
	 *
	 * @return void
	 */
	protected function new_query_name()
	{
		$this->_name = null;
	}

	/**
	 * Get name query string
	 *
	 * @return string
	 */
	protected function parse_query_name()
	{
		$name = $this->_name;
		if (!$this->_name && is_callable([$this, '_default_name']))
			$name = $this->_default_name();
		return $name ? $this->_backtick($name) : '';
	}
}
