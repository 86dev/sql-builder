<?php

namespace SQLBuilder\Statements;

use SQLBuilder\Join;

/**
 * DELETE statement
 *
 * @version 1.0
 * @author 86Dev
 */
class Delete extends Query
{
	use Traits\JoinsTrait;
	use Traits\ConditionsTrait;

	#region Variables
	protected $_values;
	protected $_from;
	protected $_using;
	#endregion

	#region Getters
	/**
	 * Get delete from
	 * @return \string[]
	 */
	public function get_from()
	{
		return $this->_from;
	}

	/**
	 * Get delete using
	 * @return \string[]
	 */
	public function get_using()
	{
		return $this->_using;
	}
	#endregion

	#region Setters
	/**
	 * Add a join
	 * @param \SQLBuilder\Join $join
	 * @return Delete
	 */
	public function join(Join $join)
	{
		$this->add_join($join);
		return $this;
	}

	/**
	 * Table to delete from
	 *
	 * @param string ...$table_name_or_alias
	 * @return Delete
	 */
	public function from(...$table_name_or_alias)
	{
		$this->_from = $table_name_or_alias;
		return $this;
	}

	/**
	 * Table to use for delete
	 *
	 * @param string $table
	 * @param string $alias
	 * @return Delete
	 */
	public function using($table, $alias = '')
	{
		if (!$alias)
			$alias = $table;
		$this->_using[$alias] = $table;
		return $this;
	}

	/**
	 * Delete conditions
	 *
	 * @param string $conditions
	 * @return Delete
	 */
	public function where($conditions)
	{
		$this->set_conditions($conditions);
		return $this;
	}
	#endregion

	/**
	 * Reset the query
	 * @return Delete
	 */
	public function new_query()
	{
		$this->new_query_conditions();
		$this->new_query_join();
		$this->_from = [];
		$this->_using = [];
		return $this;
	}

	/**
	 * Get the query SQL
	 * @return string
	 */
	public function parse_query()
	{
		$nl = $this->_prettify ? "\n" : ' ';
		$nlt = $this->_prettify ? "\n\t" : ' ';
		$from = implode(",$nlt", array_map([$this, '_backtick'], $this->_from));
		$using = implode(",$nlt", array_map(function ($alias, $table) {return $this->_backtick($table).($alias !== $table ? ' AS '.$this->_backtick($alias) : ''); }, array_keys($this->_using), $this->_using));
		if ($using)
			$using = $nl.'USING '.$using;
		$join = implode($nlt, array_map(function(Join $join) use($nl) { return $nl.$join->parse_query(); }, $this->_joins));
		$where = count($this->_conditions) ? $nl.'WHERE '.$this->parse_query_conditions() : '';

		return "DELETE FROM $from$using$join$where";
	}

	/**
	 * Create a new DELETE statement
	 *
	 * @return Delete
	 */
	public static function create()
	{
		return new static();
	}
}