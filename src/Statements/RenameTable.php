<?php

namespace SQLBuilder\Statements;

/**
 * RENAME TABLE statement
 *
 * @version 1.0
 * @author 86Dev
 */
class RenameTable extends Query
{
	#region Variables
	protected $_name;
	protected $_new_name;
	#endregion

	#region Getters
	/**
	 * Get table name(s)
	 *
	 * @return string|string[]
	 */
	public function get_name()
	{
		return $this->_name;
	}

	/**
	 * Get new table name(s)
	 *
	 * @return string|string[]
	 */
	public function get_new_name()
	{
		return $this->_new_name;
	}
	#endregion

	#region Setters
	/**
	 * Set table name(s)
	 *
	 * @param string|string[] $name Original table(s) name(s)
	 * @return RenameTable
	 */
	public function name($name)
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * Set new table name(s)
	 *
	 * @param string|string[] $name New table(s) name(s)
	 * @return RenameTable
	 */
	public function new_name($new_name)
	{
		$this->_new_name = $new_name;
		return $this;
	}
	#endregion

	public function __construct($name = null, $new_name = null)
	{
		$this->name($name);
		$this->new_name($new_name);
	}

	/**
	 * Reset the query
	 *
	 * @return RenameTable
	 */
	public function new_query()
	{
		$this->_name = null;
		$this->_new_name = null;
		return $this;
	}

	/**
	 * Get the query SQL
	 *
	 * @return string
	 */
	public function parse_query()
	{
		if (!$this->_name)
			throw new \UnexpectedValueException("Rename table : original table name must be set");
		if (!$this->_new_name)
			throw new \UnexpectedValueException("Rename table : new table name must be set");

		$names = is_array($this->_name) ? $this->_name : [$this->_name];
		$new_names = is_array($this->_new_name) ? $this->_new_name : [$this->_new_name];
		if (count($names) !== count($new_names))
			throw new \UnexpectedValueException(sprintf("Rename table : new table name count (%d) must be the same as original table name count (%d).", count($names), count($new_names)));

		$nlt = $this->_prettify ? "\n\t" : '';
		return "RENAME TABLE ".implode(', '.$nlt, array_map(function($name, $new_name) { return $this->_backtick($name).' TO '.$this->_backtick($new_name); }, $names, $new_names));
	}

	/**
	 * Create a new RENAME TABLE query
	 *
	 * @param string|string[] $name Original table(s) name(s)
	 * @param string|string[] $new_name New table(s) name(s)
	 * @return RenameTable
	 */
	public static function create($name = null, $new_name = null)
	{
		return (new static())->name($name)->new_name($new_name);
	}
}