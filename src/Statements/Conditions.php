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
	 * @param string[] $conditions
	 */
	public function conditions($conditions): self
	{
		$this->set_conditions($conditions);
		return $this;
	}

	/**
	 * Reste conditions query value
	 */
	public function new_query(): self
	{
		$this->new_query_conditions();
		return $this;
	}

	/**
	 * Get conditions query string
	 */
	public function parse_query(): string
	{
		return $this->parse_query_conditions();
	}

	/**
	 * Create a new condition
	 *
	 * @param string[] $conditions
	 */
	public static function create($conditions = null): self
	{
		return (new static())->conditions($conditions);
	}
}
