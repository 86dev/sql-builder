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
	 * @param \string[] $conditions
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
	 * @param \string[] $conditions
	 * @return void
	 */
	public static function create($conditions = null)
	{
		return (new static())->conditions($conditions);
	}
}
