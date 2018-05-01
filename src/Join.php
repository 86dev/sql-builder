<?php
namespace SQLBuilder;

/**
 * Join query builder
 *
 * @method Join table
 *
 * @version 1.0
 * @author 86Dev
 */
class Join extends Statements\Query
{
	use Statements\Traits\TableTrait;
	use Statements\Traits\AliasTrait;
	use Statements\Traits\ConditionsTrait;

	#region Variables
	/**
	 * Join direction
	 * @var \string
	 */
	protected $_direction;

	/**
	 * Join type
	 * @var \string
	 */
	protected $_type;
	#endregion

	#region Getters
	/**
	 * Get join direction
	 * @return \string
	 */
	public function get_direction()
	{
		return $this->_direction;
	}

	/**
	 * Get join type
	 * @return \string
	 */
	public function get_type()
	{
		return $this->_type;
	}
	#endregion

	#region Setters

	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	public function alias($alias)
	{
		$this->set_alias($alias);
		return $this;
	}

	public function on($conditions)
	{
		$this->set_conditions($conditions);
		return $this;
	}

	/**
	 * Set join direction to left
	 */
	public function left()
	{
		$this->_direction = 'LEFT';
		return $this;
	}

	/**
	 * Set join direction to right
	 */
	public function right()
	{
		$this->_direction = 'RIGHT';
		return $this;
	}

	/**
	 * Set join type to inner
	 */
	public function inner()
	{
		$this->_type = 'INNER';
		$this->_direction = null;
		return $this;
	}

	/**
	 * Set join type to outer
	 */
	public function outer()
	{
		$this->_type = 'OUTER';
		return $this;
	}
	#endregion

	#region Helpers
	public static function left_inner_join($table = null, $alias = null)
	{
		return (new Join($table, $alias))->left()->inner();
	}

	public static function left_outer_join($table = null, $alias = null)
	{
		return (new Join($table, $alias))->left()->outer();
	}

	public static function inner_join($table = null, $alias = null)
	{
		return (new Join($table, $alias))->inner();
	}

	public static function right_inner_join($table = null, $alias = null)
	{
		return (new Join($table, $alias))->right()->inner();
	}

	public static function right_outer_join($table = null, $alias = null)
	{
		return (new Join($table, $alias))->right()->outer();
	}
	#endregion

	/**
	 * Constructor allowing to specify columns and action
	 * @param \string[] $columns
	 * @param \string $action
	 */
	public function __construct($table = null, $alias = null)
	{
		parent::__construct();
		$this->table($table);
		$this->alias($alias);
	}

	/**
	 * Reset the query
	 * @return Index
	 */
	public function new_query()
	{
		return $this->new_query_table()->new_query_alias()->new_query_conditions()->left()->outer();
	}

	/**
	 * Get the query SQL
	 * @return \string
	 */
	public function parse_query()
	{
		$this->sql = trim("$this->_direction $this->_type JOIN {$this->parse_query_table()}{$this->parse_query_alias()} ON {$this->parse_query_conditions()}");
		return $this->sql;
	}
}