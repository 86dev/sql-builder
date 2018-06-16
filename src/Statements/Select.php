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
	 * Add a join
	 *
	 * @param \SQLBuilder\Join $join
	 */
	public function join($join)
	{
		$this->add_join($join);
		return $this;
	}

	/**
	 * Reset the query
	 *
	 * @return Select
	 */
	public function new_query()
	{
		$this->_select = new Fields();
		$this->_from = [];
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

		$distinct = $this->_distinct ? 'DISTINCT ' : '';
		$select = 'SELECT '.$distinct.$this->_select->prettify($this->_prettify)->parse_query();
		$from = $nl.'FROM '.implode(', '.$nl, array_map(function($table, $alias) { return $this->_backtick($table).($table !== $alias ? ' AS '.$this->_backtick($alias) : '') ; }, $this->_from, array_keys($this->_from)));

		$joins = implode($nl, array_map(function(Join $join) { return $join->prettify($this->_prettify)->parse_query(); }, $this->_joins));
		if ($joins)
			$joins = $nl.$joins;

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

		$this->sql = $select.$from.$joins.$where.$groupby.$having.$orderby.$limit.$offset;
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

		$this->_from[$alias] = $table;
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
	 * Create a new SELECT query
	 *
	 * @return Select
	 */
	public static function create()
	{
		return new static();
	}
}