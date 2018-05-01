<?php

namespace SQLBuilder\Statements;

use SQLBuilder\Condition;

/**
 * RENAME TABLE statement
 *
 * @version 1.0
 * @author 86Dev
 */
class Update extends Query
{
	use Traits\TableTrait;
	use Traits\AliasTrait;
	use Traits\JoinsTrait;
	use Traits\ConditionsTrait;

	#region Variables
	protected $_values;
	#endregion

	#region Getters
	/**
	 * Get update values
	 * @return \string[]
	 */
	public function get_values()
	{
		return $this->_values;
	}
	#endregion

	#region Setters
	/**
	 * Add a join
	 * @param Join $join
	 */
	public function join($join)
	{
		$this->add_join($join);
		return $this;
	}

	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	public function alias($alias)
	{
		$this->set_alias($alias);
		return $this;
	}

	public function where($conditions)
	{
		$this->set_conditions($conditions);
		return $this;
	}

	/**
	 * Add a value.
	 * @param \string $field Field name with alias if needed
	 * @param mixed $value Value
	 * @return Update
	 */
	public function set($field, $value, $type = null)
	{
		if (!$field || !is_string($field))
			throw new \UnexpectedValueException("Update $this->_table : field must be defined");
		$this->_values[$field] = Condition::eq($field, $value, $type);
		return $this;
	}

	/**
	 * Set all values at once. Do not replace values already set, except if the field already exists. The field type will be infered from the value type.
	 * @param string[] $values An array of field => value
	 * @return Update
	 */
	public function set_many($values)
	{
		if (!is_array($values))
			throw new \UnexpectedValueException('Update $this->_table : values must be an array');
		foreach ($values as $field => $value)
		{
			$this->set($field, $value);
		}
		return $this;
	}
	#endregion

	/**
	 * Reset the query
	 * @return Update
	 */
	public function new_query()
	{
		$this->new_query_alias();
		$this->new_query_conditions();
		$this->new_query_join();
		$this->new_query_table();
		$this->_values = [];
		return $this;
	}

	/**
	 * Get the query SQL
	 * @return string
	 */
	public function parse_query()
	{
		if (!count($this->_values))
			throw new \UnexpectedValueException("Update $this->_table : values must be set");

		$nl = $this->_prettify ? "\n" : ' ';
		$nlt = $this->_prettify ? "\n\t" : ' ';
		$values = implode(",$nlt", $this->_values);
		$where = count($this->_conditions) ? $nl.'WHERE '.$this->parse_query_conditions() : '';

		return "UPDATE {$this->parse_query_table()}{$this->parse_query_alias()}{$nl}SET $values$where";
	}

	public static function create()
	{
		return new static();
	}
}