<?php
namespace SQLBuilder\Statements;
use SQLBuilder\SQL;

/**
 * Base SQL query builder
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class Query
{
	#region Variables
	protected $_prettify = false;
	public $sql;
	#endregion

	public function __construct()
	{
		$this->new_query();
	}

	/**
	 * Reset the query
	 * @return Query
	 */
	public abstract function new_query();

	/**
	 * Get the query SQL
	 * @return \string
	 */
	public abstract function parse_query();

	/**
	 * Surround a name with bactick. Shortcut function to SQLHelper::backtick
	 * @param \string $name
	 * @return \string
	 */
	protected function _backtick($name)
	{
		return SQL::backtick($name);
	}

	/**
	 * Surround a value with quote. Shortcut function to SQLHelper::quote
	 * @param \string $value
	 * @return \string
	 */
	protected function _quote($value)
	{
		return SQL::quote($value);
	}

	/**
	 * Indicates if the SQL returned should be prettified
	 * @param \bool $prettify
	 * @return static
	 */
	public function prettify($prettify = true)
	{
		$this->_prettify = $prettify;
		return $this;
	}

	/**
	 * Get the default query name
	 *
	 * @return string
	 */
	protected function _default_name()
	{
		return '';
	}
}
