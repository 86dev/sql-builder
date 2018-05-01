<?php
namespace SQLBuilder\Statements;

/**
 * Base SQL insert builder
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class InsertBase extends Query
{
	use Traits\TableTrait;
	use Traits\FieldsTrait;

	protected $_ignore;

	/**
	 * Get insert ignore
	 * @return \bool
	 */
	public function get_ignore()
	{
		return $this->_ignore;
	}

	/**
	 * Set whether the value must be ignored if an error occured
	 * @param \bool $ignore
	 */
	public function set_ignore($ignore = true)
	{
		$this->_ignore = $ignore;
		return $this;
	}

	/**
	 * Reset the query
	 * @return InsertBase
	 */
	function new_query()
	{
		$this->new_query_table();
		$this->new_query_fields();
		$this->_ignore = false;
		return $this;
	}

	/**
	 * Get the insert SQL statement
	 * @return string
	 */
	function parse_query()
	{
		if (!$this->_table)
			throw new \UnexpectedValueException("Insert : table name must be set");
		$columns = count($this->_fields) ? " ({$this->parse_query_fields()})" : '';
		$ignore = $this->_ignore ? ' IGNORE' : '';
		return "INSERT$ignore INTO {$this->parse_query_table()}$columns";
	}
}
