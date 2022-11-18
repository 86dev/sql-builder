<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define alias properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait AliasTrait
{
	/**
	 * Alias
	 *
	 * @var string
	 */
	protected $_alias;

	/**
	 * Get alias
	 * @return string
	 */
	public function get_alias()
	{
		return $this->_alias;
	}

	/**
	 * Set alias
	 *
	 * @param string $alias
	 */
	protected function set_alias($alias)
	{
		$this->_alias = $alias;
	}

	/**
	 * Reset alias query value
	 *
	 * @return void
	 */
	protected function new_query_alias()
	{
		$this->_alias = null;
	}

	/**
	 * Get alias query string
	 *
	 * @return string
	 */
	protected function parse_query_alias()
	{
		return $this->_alias ? " AS {$this->_backtick($this->_alias)}" : '';
	}
}
