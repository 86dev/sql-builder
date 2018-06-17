<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define table properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait TableTrait
{
	/**
	 * Table name
	 *
	 * @var \string
	 */
	protected $_table;

	/**
	 * Get table name
	 *
	 * @return \string
	 */
	public function get_table()
	{
		return $this->_table;
	}

	/**
	 * Set table name
	 *
	 * @param \string $table
	 */
	protected function set_table($table)
	{
		$this->_table = $table;
	}

	/**
	 * Reset table query value
	 *
	 * @return void
	 */
	protected function new_query_table()
	{
		$this->_table = null;
		return $this;
	}

	/**
	 * Get table query string
	 *
	 * @return string
	 */
	protected function parse_query_table()
	{
		return $this->_backtick($this->_table);
	}
}
