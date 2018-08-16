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
	 *
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
	 *
	 * @param Join $join
	 * @return Update
	 */
	public function join($join)
	{
		$this->add_join($join, $this->_table);
		return $this;
	}

	/**
	 * Set table name
	 *
	 * @param string $table
	 * @return Update
	 */
	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	/**
	 * Set table alias
	 *
	 * @param string $alias
	 * @return Update
	 */
	public function alias($alias)
	{
		$this->set_alias($alias);
		return $this;
	}

	/**
	 * Set update conditions
	 *
	 * @param string|string[] $conditions
	 * @return Update
	 */
	public function where($conditions)
	{
		$this->set_conditions($conditions);
		return $this;
	}

	/**
	 * Add a value.
	 *
	 * @param string $field Field name with alias if needed
	 * @param mixed $value Value
	 * @param string $table Table name or alias
	 * @param string $type Value type
	 * @return Update
	 */
	public function set($field, $value, $table = null, $type = null)
	{
		if (!$field || !is_string($field))
			throw new \UnexpectedValueException("Update $this->_table : field must be defined");
		$this->_values[$field] = Condition::eq($field, $value, $table, $type);
		return $this;
	}

	/**
	 * Add a value
	 *
	 * @param Condition $condition
	 * @return Update
	 */
	public function setCondition($condition)
	{
		if (!$condition || !is_a($condition, Condition::class))
			throw new \UnexpectedValueException("Update $this->_table : condition must be defined");
		$this->_values[$condition->get_field()] = $condition;
		return $this;
	}

	/**
	 * Set all values at once. Do not replace values already set, except if the field already exists. The field type will be infered from the value type.
	 *
	 * @param string[]|Condition[] $values An array of field => value or an array of Condition
	 * @return Update
	 */
	public function set_many($values)
	{
		if (!is_array($values))
			throw new \UnexpectedValueException('Update $this->_table : values must be an array');
		foreach ($values as $field => $value)
		{
			if (is_a($value, Condition::class))
				$this->setCondition($value);
			else
				$this->set($field, $value);
		}
		return $this;
	}
	#endregion

	/**
	 * Reset the query
	 *
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
	 *
	 * @return string
	 */
	public function parse_query()
	{
		if (!count($this->_values))
			throw new \UnexpectedValueException("Update $this->_table : values must be set");

		$nl = $this->_prettify ? "\n" : ' ';
		$nlt = $this->_prettify ? "\n\t" : ' ';
		$values = [];
		foreach ($this->_values as $value)
		{
			if (is_a($value, Condition::class))
				$values[] = $value->keep_null()->parse_query();
			else if (is_array($value))
				$values[] = Condition::fromArray($value)->keep_null()->parse_query();
			else
				$values[] = $value;
		}
		$values = implode(",$nlt", $values);
		$join = $this->parse_query_join($this->_table).$nl;
		$where = count($this->_conditions) ? $nl.'WHERE '.$this->parse_query_conditions() : '';

		return "UPDATE {$this->parse_query_table()}{$this->parse_query_alias()}{$join}SET $values$where";
	}

	/**
	 * Create a new UPDATE query
	 *
	 * @return Update
	 */
	public static function create()
	{
		return new static();
	}
}