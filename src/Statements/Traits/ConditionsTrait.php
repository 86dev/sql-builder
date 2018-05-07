<?php

namespace SQLBuilder\Statements\Traits;
use SQLBuilder\Condition;
use SQLBuilder\Enums\ConditionRelation;

/**
 * Define conditions properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait ConditionsTrait
{
	/**
	 * Conditions
	 * @var \string[]
	 */
	protected $_conditions;

	/**
	 * Get conditions
	 * @return \string[]
	 */
	public function get_conditions()
	{
		return $this->_conditions;
	}

	/**
	 * Set conditions
	 * @param \string[] $conditions
	 * @return static
	 */
	protected function set_conditions($conditions)
	{
		if (!is_array($conditions))
			$conditions = [$conditions];
		foreach ($conditions as $key => $condition)
		{
			if (is_string($key) && $key !== 'relation')
				$conditions[$key] = Condition::make($key, $condition);
		}

		$this->_conditions = array_merge($this->_conditions, $conditions);
		return $this;
	}

	protected function new_query_conditions()
	{
		$this->_conditions = ['relation' => ConditionRelation::_AND];
		return $this;
	}

	protected function parse_query_conditions()
	{
		return $this->conditions_walker($this->_conditions);
	}

	/**
	 * Walk through the conditions array to generate a WHERE/ON clause
	 * @param \string[] $conditions
	 * @return \string
	 */
	protected function conditions_walker($conditions)
	{
		if (!array_key_exists('relation', $conditions))
		{
			$conditions['relation'] = ConditionRelation::_AND;
		}
		$sql = '';
		$relation = $conditions['relation'];
		if (!ConditionRelation::isValidValue($relation))
			$relation = ConditionRelation::_AND;

		foreach ($conditions as $key => $condition)
		{
			if ($key === 'relation') continue;

			if ($sql)
				$sql .= " $relation ";

			if (is_array($condition))
				$sql .= $this->conditions_walker($condition);
			else
				$sql .= $condition;
		}
		if ($sql && count($conditions) > 2) // 2 => relation + 1 value
			$sql = "($sql)";
		return $sql;
	}
}