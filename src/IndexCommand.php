<?php
namespace SQLBuilder;

use SQLBuilder\Enums\SQLAction;

/**
 * Index command query builder
 *
 * This class is intended to create independant INDEX commands that must not be used inside CREATE or ALTER TABLE statements.
 * See Index class to build commands related to a CREATE or ALTER TABLE statement.
 *
 * @version 1.0
 * @author 86Dev
 */
class IndexCommand extends Statements\Query
{
	use Statements\Traits\IndexTrait;

	#region Constants
	const ACTIONS = [SQLAction::CREATE, SQLAction::DROP];
	#endregion

	public function __construct()
	{
		$this->_allow_empty_action = false;
	}

	public function name($name)
	{
		$this->set_name($name);
		return $this;
	}

	public function action($action)
	{
		$this->set_action($action);
		return $this;
	}

	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	public function comment($comment)
	{
		$this->set_comment($comment);
		return $this;
	}

	public function field($field, $table = '', $alias = '')
	{
		$this->add_field($field, $table, $alias);
		return $this;
	}

	public function fields(...$fields)
	{
		$this->set_fields(...$fields);
		return $this;
	}

	public function unique($unique = true)
	{
		$this->set_unique($unique);
		return $this;
	}

	public function fulltext($fulltext = true)
	{
		$this->set_fulltext($fulltext);
		return $this;
	}

	public function spatial($spatial = true)
	{
		$this->set_spatial($spatial);
		return $this;
	}

	public function parse_query()
	{
		$name = $this->parse_query_name();
		if (!$this->_table)
			throw new \UnexpectedValueException("Index $name: table name is required.");

		if ($this->_action === SQLAction::DROP)
		{
			$table = $this->parse_query_table();
			$this->sql = "DROP INDEX $name ON $table";
		}
		else
		{
			$columns = $this->parse_query_fields();
			if (!$columns && $this->_action !== SQLAction::DROP)
				throw new \UnexpectedValueException("Index $name: The columns to which the index refers must be set to create it.");

			$action = $this->parse_query_action();
			$comment = $this->parse_query_comment();
			$type = $this->parse_query_fulltext().$this->parse_query_spatial().$this->parse_query_unique();
			$this->sql = "$action$type INDEX {$this->parse_query_name()} ($columns)$comment";
		}
		return $this->sql;
	}

	public static function create()
	{
		return new static();
	}
}