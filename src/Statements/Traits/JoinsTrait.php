<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define joins properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait JoinsTrait
{
	/**
	 * Joins
	 *
	 * @var \SQLBuilder\Join[][]
	 */
	protected $_joins;

	/**
	 * Get joins
	 *
	 * @return \SQLBuilder\Join[][]
	 */
	public function get_joins()
	{
		return $this->_joins;
	}

	/**
	 * Add a join
	 *
	 * @param \SQLBuilder\Join $join
	 */
	protected function add_join(\SQLBuilder\Join $join, $table)
	{
		if (!is_a($join, \SQLBuilder\Join::class))
			throw new \UnexpectedValueException('Join must be a '.\SQLBuilder\Join::class);
		if (!is_string($table) || !$table)
			throw new \UnexpectedValueException('Join must be associated with a valid table name.');
		$this->_joins[$table][] = $join;
	}

	/**
	 * Reset join query value
	 *
	 * @return void
	 */
	protected function new_query_join()
	{
		$this->_joins = [];
		return $this;
	}

	/**
	 * Get join query string
	 *
	 * @return string
	 */
	protected function parse_query_join($table)
	{
		if (!count($this->_joins))
			return '';

		$nl = $this->_prettify ? "\n" : '';
		$nlt = $this->_prettify ? "\n\t" : ' ';

		$joins = call_user_func_array('array_merge', $this->_joins);
		return $nlt.implode($nlt, array_map(function(\SQLBuilder\Join $join) { return $join->prettify($this->_prettify)->parse_query(); }, $joins)).$nl;
	}
}
