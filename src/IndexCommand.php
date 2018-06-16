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

	/**
	 * Set index name
	 *
	 * @param string $name
	 * @return IndexCommand
	 */
	public function name($name)
	{
		$this->set_name($name);
		return $this;
	}

	/**
	 * Set Index action
	 *
	 * @param string $action
	 * @return IndexCommand
	 */
	public function action($action)
	{
		$this->set_action($action);
		return $this;
	}

	/**
	 * Set Index table
	 *
	 * @param string $table
	 * @return IndexCommand
	 */
	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	/**
	 * Set index comment
	 *
	 * @param string $comment
	 * @return IndexCommand
	 */
	public function comment($comment)
	{
		$this->set_comment($comment);
		return $this;
	}

	/**
	 * Add a field
	 *
	 * @param string $field
	 * @param string $table
	 * @param string $alias
	 * @return IndexCommand
	 */
	public function field($field, $table = '', $alias = '')
	{
		$this->add_field($field, $table, $alias);
		return $this;
	}

	/**
	 * Set fields
	 *
	 * @param string ...$fields
	 * @return IndexCommand
	 */
	public function fields(...$fields)
	{
		$this->set_fields(...$fields);
		return $this;
	}

	/**
	 * Set if index is unique
	 *
	 * @param boolean $unique
	 * @return IndexCommand
	 */
	public function unique($unique = true)
	{
		$this->set_unique($unique);
		return $this;
	}

	/**
	 * Set if index is fulltext
	 *
	 * @param boolean $fulltext
	 * @return IndexCommand
	 */
	public function fulltext($fulltext = true)
	{
		$this->set_fulltext($fulltext);
		return $this;
	}

	/**
	 * Set if index is spatial
	 *
	 * @param boolean $spatial
	 * @return IndexCommand
	 */
	public function spatial($spatial = true)
	{
		$this->set_spatial($spatial);
		return $this;
	}

	/**
	 * Reset the query
	 *
	 * @return IndexCommand
	 */
	public function new_query()
	{
		$this->new_query_index();
		return $this;
	}

	/**
	 * Get the query SQL
	 *
	 * @return string
	 */
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

	/**
	 * Create a new INDEX query
	 *
	 * @return IndexCommand
	 */
	public static function create()
	{
		return new static();
	}
}