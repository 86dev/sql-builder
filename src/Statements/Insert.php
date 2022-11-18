<?php

namespace SQLBuilder\Statements;

use SQLBuilder\Condition;


/**
 * RENAME TABLE statement
 *
 * @version 1.0
 * @author 86Dev
 */
class Insert extends InsertBase
{
	#region Variables
	protected $_values;
	#endregion

	#region Getters

	/**
	 * Get insert values
	 * @return string
	 */
	public function get_values()
	{
		return $this->_values;
	}
	#endregion

	#region Setters
	/**
	 * The table to insert into
	 *
	 * @param string $table
	 * @return Insert
	 */
	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	/**
	 * Add a field to insert
	 *
	 * @param string $field
	 * @param string $table
	 * @param string $alias
	 * @param boolean $do_not_use_backtick
	 * @return Insert
	 */
	public function field($field, $table = '', $alias = '', $do_not_use_backtick = false)
	{
		$this->add_field($field, $table, $alias, $do_not_use_backtick);
		return $this;
	}

	/**
	 * Set a list of fields to insert
	 *
	 * @param string[] ...$fields
	 * @return Insert
	 */
	public function fields(...$fields)
	{
		$this->set_fields($fields);
		return $this;
	}

	/**
	 * Set whether the value must be ignored if an error occured
	 * @param bool $ignore
	 * @return Insert
	 */
	public function ignore($ignore = true)
	{
		$this->set_ignore($ignore);
		return $this;
	}

	/**
	 * Add an array of values to insert. Call this method multiple times to set multiple VALUES statement or call Insert::multiple_values to set all VALUES statement at once.
	 * @param string[]|array $values Values to insert for one row
	 * @return Insert
	 */
	public function values($values)
	{
		if (!is_array($values) || !count($values))
			throw new \UnexpectedValueException('Index : values can not be empty');
		if (is_array(current($values)))
		{
			return $this->values($values);
		}
		$this->_values[] = $values;
		return $this;
	}

	/**
	 * Sett all VALUES statement at once. Call Insert::values to add a single VALUES row.
	 * @param mixed $multi_values
	 * @return Insert
	 */
	public function multiple_values($multi_values)
	{
		if (!is_array($multi_values) || !count($multi_values))
			throw new \UnexpectedValueException('Index : multiple values can not be empty');
		if (!is_array(current($multi_values)))
		{
			return $this->values($multi_values);
		}
		$this->_values = $multi_values;
		return $this;
	}
	#endregion

	/**
	 * Reset the query
	 * @return Insert
	 */
	public function new_query()
	{
		parent::new_query();
		$this->_values = [];
		return $this;
	}

	/**
	 * Get the query SQL
	 * @return string
	 */
	public function parse_query()
	{
		$sql = parent::parse_query();

		if (!count($this->_values))
			throw new \UnexpectedValueException("Insert : values must be set");

		$nl = $this->_prettify ? "\n" : ' ';
		$nlt = $this->_prettify ? "\n\t" : ' ';
		$values = implode(",$nlt", array_map(function($values) { return '('.implode(", ", array_map(function($value) { return Condition::prepare_value($value, null, true); }, $values)).')'; }, $this->_values));

		return "$sql{$nl}VALUES $values";
	}

	/**
	 * Create a new Insert statement
	 *
	 * @return Insert
	 */
	public static function create()
	{
		return new static();
	}
}