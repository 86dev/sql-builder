<?php

namespace SQLBuilder\Statements;

/**
 * RENAME TABLE statement
 *
 * @version 1.0
 * @author 86Dev
 */
class InsertSelect extends InsertBase
{
	#region Variables
	protected $_select;
	#endregion

	#region Getters
	/**
	 * Get insert select
	 * @return Select
	 */
	public function get_select()
	{
		return $this->_select;
	}
	#endregion

	#region Setters

	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	public function field($field, $table = '', $alias = '', $do_not_use_backtick = false)
	{
		$this->add_field($field, $table, $alias, $do_not_use_backtick);
		return $this;
	}

	public function fields(...$fields)
	{
		$this->set_fields($fields);
		return $this;
	}

	/**
	 * Set whether the value must be ignored if an error occured
	 * @param \bool $ignore
	 */
	public function ignore($ignore = true)
	{
		$this->set_ignore($ignore);
		return $this;
	}

	/**
	 * Set the SELECT statement
	 * @param Select $select
	 * @return InsertSelect
	 */
	public function select($select)
	{
		if (!is_a($select, Select::class))
			throw new \UnexpectedValueException('Index : select must be a \SQLBuilder\Select instance');
		$this->_select = $select;
		return $this;
	}
	#endregion

	/**
	 * Reset the query
	 * @return InsertSelect
	 */
	public function new_query()
	{
		parent::new_query();
		$this->_select = null;
		return $this;
	}

	/**
	 * Get the query SQL
	 * @return string
	 */
	public function parse_query()
	{
		$sql = parent::parse_query();

		if (!$this->_select)
			throw new \UnexpectedValueException("Insert : select must be set");

		$nl = $this->_prettify ? "\n" : ' ';

		$this->_select->prettify($this->_prettify);
		$select = $this->_select->parse_query();

		return "$sql$nl$select";
	}

	public static function create()
	{
		return new static();
	}
}