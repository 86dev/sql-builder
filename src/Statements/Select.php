<?php
namespace SQLBuilder\Statements;
use SQLBuilder\Join;
use SQLBuilder\Statements\Conditions;

/**
 * SELECT statement builder
 *
 * @version 1.0
 * @author 86Dev
 */
class Select extends Query
{
	use Traits\JoinsTrait;

	#region Constants
	const ORDER_ASC = 'ASC';
	const ORDER_DESC = 'DESC';
	const ORDERS = [self::ORDER_ASC, self::ORDER_DESC];
	#endregion

	#region Variables
	/**
	 * Select
	 *
	 * @var Fields
	 */
	protected $_select;

	/**
	 * Distinct
	 *
	 * @var \bool
	 */
	protected $_distinct;

	/**
	 * From
	 *
	 * @var \string[]
	 */
	protected $_from = [];

	/**
	 * Last table name or alias added
	 *
	 * @var string
	 */
	protected $_last_from = null;

	/**
	 * Where
	 *
	 * @var Conditions
	 */
	protected $_where;

	/**
	 * Group by
	 *
	 * @var Fields
	 */
	protected $_groupby;

	/**
	 * Having
	 *
	 * @var Conditions
	 */
	protected $_having;

	/**
	 * Order by ['field' => 'ASC|DESC']
	 *
	 * @var \string[]
	 */
	protected $_orderby = [];

	/**
	 * Limit
	 *
	 * @var \int|null
	 */
	protected $_limit = '';

	/**
	 * Offset
	 *
	 * @var \int|null
	 */
	protected $_offset = null;

	/**
	 * SQL result
	 *
	 * @var \string
	 */
	public $sql = '';
	#endregion

	/**
	 * Reset the query
	 *
	 * @return Select
	 */
	public function new_query()
	{
		$this->_select = new Fields();
		$this->_from = [];
		$this->_last_from = null;
		$this->_joins = [];
		$this->_where = new Conditions();
		$this->_groupby = new Fields();
		$this->_having = new Conditions();
		$this->_orderby = [];
		$this->_limit = null;
		$this->_offset = null;
		$this->_sql = '';
		return $this;
	}

	/**
	 * Get the query SQL
	 *
	 * @return \string
	 */
	public function parse_query()
	{
		$nl = $this->_prettify ? "\n" : ' ';
		$nlt = $this->_prettify ? "\n\t" : ' ';

		$distinct = $this->_distinct ? 'DISTINCT ' : '';
		$select = 'SELECT '.$distinct.$this->_select->prettify($this->_prettify)->parse_query();
		$from = $nl.'FROM '.implode(', '.$nl, array_map(function($table, $alias) { return $this->_backtick($table).($table !== $alias ? ' AS '.$this->_backtick($alias) : '') ; }, $this->_from, array_keys($this->_from)));

		$from = '';
		foreach ($this->_from as $alias => $table)
		{
			if ($from) $from .= ",$nl";
			$from .= $this->_backtick($table).($table !== $alias ? ' AS '.$this->_backtick($alias) : '');
			if (array_key_exists($alias, $this->_joins) && count($this->_joins[$alias]))
				$from .= $nlt.implode($nlt, array_map(function(Join $join) { return $join->prettify($this->_prettify)->parse_query(); }, $this->_joins[$alias]));
		}
		$from = "{$nl}FROM $from";

		$where = $this->_where->prettify($this->_prettify)->parse_query();
		if ($where)
			$where = $nl.'WHERE '.$where;

		$groupby = $this->_groupby->prettify($this->_prettify)->parse_query();
		$having = '';
		if ($groupby)
		{
			$groupby = $nl.'GROUP BY '.$groupby;
			$having = $this->_having->prettify($this->_prettify) ->parse_query();
			if ($having)
				$having = $nl.'HAVING '.$having;
		}

		$orderby = implode(', ', $this->_orderby);
		if ($orderby)
			$orderby = $nl.'ORDER BY '.$orderby;

		$limit = '';
		if ($this->_limit !== null)
			$limit = $nl.'LIMIT '.$this->_limit;

		$offset = '';
		if ($this->_offset !== null)
			$offset = $nl.'OFFSET '.$this->_offset;

		$this->sql = $select.$from.$where.$groupby.$having.$orderby.$limit.$offset;
		return $this->sql;
	}

	/**
	 * Add a field to the query SELECT clause
	 *
	 * @param \string $field The field name
	 * @param \string $table The field's table name or alias (must be consistent with tables added in Select::from or Select::join)
	 * @param \string $alias The field alias
	 * @param \boolean $group_by Also add this field to the query GROUP BY clause
	 * @return Select
	 */
	public function select($field, $table = '', $alias = '', $group_by = false, $do_not_use_backtick = false)
	{
		$this->_select->field($field, $table, $alias, $do_not_use_backtick);
		if ($group_by)
			$this->groupby($field, $table, $do_not_use_backtick);
		return $this;
	}

	/**
	 * Add a table to the query FROM clause
	 *
	 * @param \string $table The table name
	 * @param \string $alias The table alias
	 * @return Select
	 */
	public function from($table, $alias = '')
	{
		if (!$alias)
			$alias = $table;

		$this->_last_from = $alias;
		$this->_from[$alias] = $table;
		return $this;
	}

	/**
	 * Add a join
	 *
	 * @param \SQLBuilder\Join $join
	 * @param string $table The table name or alias on which the join is applied. If not provided, the join will be associated with the latest table added via 'from'
	 * @return Select
	 */
	public function join(Join $join, $table = null)
	{
		if (!$table) $table = $this->_last_from;
		$this->add_join($join, $table);
		return $this;
	}

	/**
	 * Add conditions to the query WHERE clause
	 *
	 * @param \string|\string[] $conditions A condition or an array of conditions. Each value should be either a string ("myField = 'a'") or an array of conditions (['relation' => Select::REL_AND, "aField = 'a'", "aField2 = 0"]. Each array should have a special value keyed 'relation' equals to Select::REL_AND or Select::REL_OR.
	 * @return Select
	 */
	public function where($conditions)
	{
		$this->_where->conditions($conditions);
		return $this;
	}

	/**
	 * Add field to the query GROUP BY clause
	 *
	 * @param \string $field The field name
	 * @param \string $table The field's table name or alias (must be consistent with tables added in Select::from or Select::join)
	 * @return Select
	 */
	public function groupby($field, $table = '', $do_not_use_backtick = false)
	{
		$this->_groupby->field($field, $table, '', $do_not_use_backtick);
		return $this;
	}

	/**
	 * Add conditions to the query HAVING clause
	 *
	 * @param \string|\string[] $conditions A condition or an array of conditions. Each value should be either a string ("myField = 'a'") or an array of conditions (['relation' => Select::REL_AND, "aField = 'a'", "aField2 = 0"]. Each array should have a special value keyed 'relation' equals to Select::REL_AND or Select::REL_OR.
	 * @return Select
	 */
	public function having($conditions)
	{
		$this->_having->conditions($conditions);
		return $this;
	}

	/**
	 * Add field to the query ORDER BY clause
	 *
	 * @param \string $field The field name or alias (must be consistent with fields added in Select::select)
	 * @param \string $order Select::ORDER_ASC or Select::ORDER_DESC
	 * @return Select
	 */
	public function orderby($field, $order = Select::ORDER_ASC)
	{
		if (!in_array($order, self::ORDERS))
			$order = self::ORDER_ASC;
		$this->_orderby[] = "{$this->_backtick($field)} $order";
		return $this;
	}

	/**
	 * Set the query LIMIT clause
	 *
	 * @param \int|null $limit The number of items to include
	 * @return Select
	 */
	public function limit($limit)
	{
		$this->_limit = $limit;
		return $this;
	}

	/**
	 * Set the query OFFSET clause
	 *
	 * @param \int|null $offset The number of items to ignore
	 * @return Select
	 */
	public function offset($offset)
	{
		$this->_offset = $offset;
		return $this;
	}

	/**
	 * Set the query DISTINCT clause
	 *
	 * @param bool $distinct if the query's results should be distinct
	 * @return Select
	 */
	public function distinct($distinct = true)
	{
		$this->_distinct = $distinct;
		return $this;
	}

	/**
	 * Create a new SELECT query
	 *
	 * @return Select
	 */
	public static function create()
	{
		return new static();
	}
}