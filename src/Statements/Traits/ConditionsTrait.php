<?php

namespace SQLBuilder\Statements\Traits;
use SQLBuilder\Condition;
use SQLBuilder\Enums\ConditionRelation;
use SQLBuilder\Enums\ConditionOperation;

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
	 *
	 * @var string[]
	 */
	protected $_conditions;

	/**
	 * Get conditions
	 *
	 * @return string[]
	 */
	public function get_conditions()
	{
		return $this->_conditions;
	}

	/**
	 * Set the first relation
	 *
	 * @param string $conditions Use {@see SQLBuilder\Enums\ConditionRelation}
	 * @return ConditionsTrait
	 */
	public function set_first_relation(string $relation): self {
		if (!in_array(trim(strtoupper($relation)), ConditionRelation::LIST)) {
			throw new \Exception('Invalid relation');
		}
		$this->_conditions['relation'] = $relation;
		return $this;
	}

	/**
	 * Set conditions
	 *
	 * @param (string|Condition|(string|Condition)[])[] $conditions
	 */
	protected function set_conditions($conditions)
	{
		if ($conditions === null) return;

		if (!is_array($conditions) || array_key_exists('field', $conditions)) {
			$conditions = [$conditions];
		}

		foreach ($conditions as $key => $condition) {
			if (is_string($key) && $key !== 'relation')
				$conditions[$key] = Condition::eq($key, $condition);
		}

		if (array_key_exists('relation', $conditions)) {
			$this->_conditions[] = $conditions;
		} else {
			$this->_conditions = array_merge($this->_conditions, $conditions);
		}
	}

	/**
	 * Reset conditions query value
	 *
	 * @return void
	 */
	protected function new_query_conditions()
	{
		$this->_conditions = ['relation' => ConditionRelation::_AND];
		return $this;
	}

	/**
	 * Get conditions query string
	 *
	 * @return string
	 */
	protected function parse_query_conditions()
	{
		return $this->conditions_walker($this->_conditions);
	}

	/**
	 * Walk through the conditions array to generate a WHERE/ON clause
	 *
	 * @param (string|Condition|(string|Condition)[])[] $conditions
	 * @return string
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

			if (is_a($condition, Condition::class))
				$sql .= $condition->parse_query();
			else if (is_array($condition))
			{
				if (array_key_exists('field', $condition))
					$sql .= Condition::fromArray($condition)->parse_query();
				else
					$sql .= $this->conditions_walker($condition);
			}
			else
				$sql .= $condition;
		}
		if ($sql && count($conditions) > 2) // 2 => relation + 1 value
			$sql = "($sql)";
		return $sql;
	}
}
