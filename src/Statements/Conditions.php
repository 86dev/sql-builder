<?php

namespace SQLBuilder\Statements;

/**
 * Conditions builder
 *
 * @version 1.0
 * @author 86Dev
 */
class Conditions extends Query
{
	use Traits\ConditionsTrait;

	/**
	 * Set conditions
	 *
	 * @param (string|Condition|(string|Condition)[])[] $conditions
	 * @return Conditions
	 */
	public function conditions($conditions)
	{
		$this->set_conditions($conditions);
		return $this;
	}

	/**
	 * Reste conditions query value
	 *
	 * @return Condition
	 */
	public function new_query()
	{
		$this->new_query_conditions();
		return $this;
	}

	/**
	 * Get conditions query string
	 *
	 * @return string
	 */
	public function parse_query()
	{
		return $this->parse_query_conditions();
	}

	/**
	 * Create a new condition
	 *
	 * @param (string|Condition|(string|Condition)[])[] $conditions
	 */
	public static function create($conditions = null): self
	{
		$result = new static();
		$result->conditions($conditions);
		if (is_array($conditions) && array_key_exists('relation', $conditions)) {
			$result->set_first_relation($conditions['relation']);
		}
		return $result;
	}
}
