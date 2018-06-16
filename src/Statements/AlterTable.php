<?php

namespace SQLBuilder\Statements;

use SQLBuilder\Index;
use SQLBuilder\Column;
use SQLBuilder\Primary;
use SQLBuilder\ForeignKey;
use SQLBuilder\Enums\SQLAction;

/**
 * ALTER TABLE statement
 *
 * @version 1.0
 * @author 86Dev
 */
class AlterTable extends Query
{
	use Traits\TableTrait;
	use Traits\CommentTrait;

	#region Variables
	protected $_new_name;
	protected $_new_index_names;
	protected $_queries;
	#endregion

	#region Setters
	/**
	 * Set the table name
	 *
	 * @return AlterTable
	 */
	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	/**
	 * Set the table comment
	 *
	 * @return AlterTable
	 */
	public function comment($comment)
	{
		$this->set_comment($comment);
		return $this;
	}

	/**
	 * Add a column
	 *
	 * @return AlterTable
	 */
	public function add_column(Column $column)
	{
		$column->action(SQLAction::ADD);
		$this->_queries[] = $column;
		return $this;
	}

	/**
	 * Add an index
	 *
	 * @return AlterTable
	 */
	public function add_index(Index $index)
	{
		$index->action(SQLAction::ADD);
		$this->_queries[] = $index;
		return $this;
	}

	/**
	 * Add a primary key
	 *
	 * @return AlterTable
	 */
	public function add_primary(Primary $primary)
	{
		$primary->action(SQLAction::ADD);
		$this->_queries[] = $primary;
		return $this;
	}

	/**
	 * Add a foreign key
	 *
	 * @return AlterTable
	 */
	public function add_foreign(ForeignKey $foreign)
	{
		$foreign->action(SQLAction::ADD);
		$this->_queries[] = $foreign;
		return $this;
	}

	/**
	 * Drop the column
	 *
	 * @return AlterTable
	 */
	public function drop_column(Column $column)
	{
		$column->action(SQLAction::DROP);
		$this->_queries[] = $column;
		return $this;
	}

	/**
	 * Drop an index
	 *
	 * @return AlterTable
	 */
	public function drop_index(Index $index)
	{
		$index->action(SQLAction::DROP);
		$this->_queries[] = $index;
		return $this;
	}

	/**
	 * Drop the primary key
	 *
	 * @return AlterTable
	 */
	public function drop_primary()
	{
		$this->_queries[] = Primary::create()->action(SQLAction::DROP);
		return $this;
	}

	/**
	 * Drop a foreign key
	 *
	 * @return AlterTable
	 */
	public function drop_foreign(ForeignKey $foreign)
	{
		$foreign->action(SQLAction::DROP);
		$this->_queries[] = $foreign;
		return $this;
	}

	/**
	 * Change a column
	 *
	 * @return AlterTable
	 */
	public function change_column(Column $column)
	{
		$column->action(SQLAction::CHANGE);
		$this->_queries[] = $column;
		return $this;
	}

	/**
	 * Modify a column
	 *
	 * @return AlterTable
	 */
	public function modify_column(Column $column)
	{
		$column->action(SQLAction::MODIFY);
		$this->_queries[] = $column;
		return $this;
	}

	/**
	 * Rename the table
	 *
	 * @return AlterTable
	 */
	public function rename_table($new_name)
	{
		$this->_new_name = $new_name;
		return $this;
	}

	/**
	 * Rename an index
	 *
	 * @return AlterTable
	 */
	public function rename_index($index_name, $new_index_name)
	{
		$this->_new_index_name[$index_name] = $new_index_name;
		return $this;
	}
	#endregion

	/**
	 * Reset the query
	 *
	 * @return AlterTable
	 */
	public function new_query()
	{
		$this->new_query_table();
		$this->new_query_comment();
		$this->_new_name = null;
		$this->_new_index_names = [];
		$this->_queries = [];
		return $this;
	}

	/**
	 * Get the query SQL
	 *
	 * @return string
	 */
	public function parse_query()
	{
		$nlt = $this->_prettify ? "\n\t" : ' ';
		$this->sql = "ALTER TABLE {$this->parse_query_table()}".$nlt;

		$queries = [];
		if ($this->_new_name)
			$queries[] = "RENAME TO ".$this->_backtick($this->_new_name);

		foreach	($this->_new_index_names as $old_name => $new_name)
		{
			$old_name = $this->_backtick($old_name);
			$new_name = $this->_backtick($new_name);
			$queries[] = "RENAME INDEX $old_name TO $new_name";
		}

		foreach ($this->_queries as $query)
		{
			$queries[] = $query->parse_query();
		}

		$this->sql .= implode(",$nlt", $queries);
		return $this->sql;
	}


	/**
	 * Create a new ALTER TABLE statement
	 *
	 * @return AlterTable
	 */
	public static function create($table = null)
	{
		return (new static())->table($table);
	}
}