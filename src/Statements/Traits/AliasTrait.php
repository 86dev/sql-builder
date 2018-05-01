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
	 * Index alias, see IndexAlias for available values
	 * @var \string
	 */
	protected $_alias;

	/**
	 * Get index alias
	 * @return \string
	 */
	public function get_alias()
	{
		return $this->_alias;
	}

	/**
	 * Set alias
	 * @param \string $alias
	 */
	public abstract function alias($alias);

	/**
	 * Set index alias, see IndexAlias for available values
	 * @param \string $alias
	 */
	protected function set_alias($alias)
	{
		$this->_alias = $alias;
		return $this;
	}

	protected function new_query_alias()
	{
		$this->_alias = null;
		return $this;
	}

	protected function parse_query_alias()
	{
		return $this->_alias ? " AS {$this->_backtick($this->_alias)}" : '';
	}
}
