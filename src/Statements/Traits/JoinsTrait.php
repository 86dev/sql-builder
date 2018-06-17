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
	 * @var \SQLBuilder\Join[]
	 */
	protected $_joins;

	/**
	 * Get joins
	 *
	 * @return \SQLBuilder\Join[]
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
	protected function add_join(\SQLBuilder\Join $join)
	{
		if (!is_a($join, \SQLBuilder\Join::class))
			throw new \UnexpectedValueException('Join must be a '.\SQLBuilder\Join::class);
		$this->_joins[] = $join;
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
	protected function parse_query_join()
	{
		if (!count($this->_joins))
			return '';

		$nl = $this->_prettify ? "\n" : '';
		$nlt = $this->_prettify ? "\n\t" : ' ';
		return $nlt.implode($nlt, array_map(function(\SQLBuilder\Join $join) { return $join->prettify($this->_prettify)->parse_query(); }, $this->_joins)).$nl;
	}
}
