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
	protected $_last_using;
	#endregion

	#region Getters
	/**
	 * Get delete from
	 * @return string[]
	 */
	public function get_from()
	{
		return $this->_from;
	}

	/**
	 * Get delete using
	 * @return string[]
	 */
	public function get_using()
	{
		return $this->_using;
	}
	#endregion

	#region Setters
	/**
	 * Add a join
	 *
	 * @param Join $join
	 * @param string $table The table name or alias on which the join is applied. If not provided, the join will be associated with the latest table added via 'using'
	 */
	public function join(Join $join, $table = null): self
	{
		if (!$table) $table = $this->_last_using;
		$this->add_join($join, $table);
		return $this;
	}

	/**
	 * Table to delete from
	 *
	 * @param string ...$table_name_or_alias
	 */
	public function from(...$table_name_or_alias): self
	{
		$this->_from = $table_name_or_alias;
		return $this;
	}

	/**
	 * Table to use for delete
	 *
	 * @param string $table The table name
	 * @param string $alias The table alias
	 */
	public function using($table, $alias = ''): self
	{
		if (!$alias)
			$alias = $table;

		$this->_using[$alias] = $table;
		$this->_last_using = $alias;
		return $this;
	}

	/**
	 * Delete conditions
	 *
	 * @param string $conditions
	 */
	public function where($conditions): self
	{
		$this->set_conditions($conditions);
		return $this;
	}
	#endregion

	/**
	 * Reset the query
	 */
	public function new_query(): self
	{
		$this->new_query_conditions();
		$this->new_query_join();
		$this->_from = [];
		$this->_using = [];
		return $this;
	}

	/**
	 * Get the query SQL
	 *
	 * @return string
	 */
	public function parse_query()
	{
		$nl = $this->_prettify ? "\n" : ' ';
		$nlt = $this->_prettify ? "\n\t" : ' ';
		$from = implode(",$nlt", array_map([$this, '_backtick'], $this->_from));
		// $using = implode(",$nlt", array_map(function ($alias, $table) {return $this->_backtick($table).($alias !== $table ? ' AS '.$this->_backtick($alias) : ''); }, array_keys($this->_using), $this->_using));

		$using = '';
		foreach ($this->_using as $alias => $table)
		{
			if ($using) $using .= ",$nl";
			$using .= $this->_backtick($table).($table !== $alias ? ' AS '.$this->_backtick($alias) : '');
			if (array_key_exists($alias, $this->_joins) && count($this->_joins[$alias]))
				$using .= $nlt.implode($nlt, array_map(function(Join $join) { return $join->prettify($this->_prettify)->parse_query(); }, $this->_joins[$alias]));
		}
		if ($using)
			$using = $nl.'USING '.$using;

		$where = count($this->_conditions) ? $nl.'WHERE '.$this->parse_query_conditions() : '';

		return "DELETE FROM $from$using$where";
	}

	/**
	 * Create a new DELETE statement
	 *
	 * @param string $from Table name to delete from
	 */
	public static function create($from = null): self
	{
		return (new static())->from($from);
	}
}