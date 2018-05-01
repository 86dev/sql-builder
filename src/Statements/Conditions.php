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

	public function conditions($conditions)
	{
		$this->set_conditions($conditions);
		return $this;
	}

	public function new_query()
	{
		$this->new_query_conditions();
		return $this;
	}

	public function parse_query()
	{
		return $this->parse_query_conditions();
	}

	public static function create()
	{
		return new static();
	}
}
