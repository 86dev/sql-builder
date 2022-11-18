<?php
namespace SQLBuilder;

use SQLBuilder\Enums\SQLAction;

/**
 * Index query builder
 *
 * This class is intended to create INDEX command that must be used inside CREATE or ALTER TABLE statements.
 * See IndexCommand to build independant commands.
 * @version 1.0
 * @author 86Dev
 */
class Index extends Statements\Query
{
	use Statements\Traits\IndexTrait;

	const ACTIONS = [SQLAction::ADD, SQLAction::DROP];

	/**
	 * Constructor allowing to specify columns and action
	 * @param string[] $columns
	 * @param string $action
	 */
	public function __construct($columns = null, $action = null)
	{
		parent::__construct();
		$this->action($action);
		if ($columns)
			$this->fields(...$columns);
	}

	/**
	 * Set index name
	 *
	 * @param string $name
	 * @return Index
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
	 * @return Index
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
	 * @return Index
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
	 * @return Index
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
	 * @return Index
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
	 * @return Index
	 */
	public function fields(...$fields)
	{
		$this->set_fields($fields);
		return $this;
	}

	/**
	 * Set if index is unique
	 *
	 * @param boolean $unique
	 * @return Index
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
	 * @return Index
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
	 * @return Index
	 */
	public function spatial($spatial = true)
	{
		$this->set_spatial($spatial);
		return $this;
	}

	/**
	 * Reset the query
	 *
	 * @return Index
	 */
	public function new_query()
	{
		$this->new_query_index();
		return $this;
	}

	/**
	 * Get the query SQL
	 *
	 * @return Index
	 */
	public function parse_query()
	{
		$this->sql = '';
		$name = $this->_default_name();

		$action = $this->_action ? $this->_action : '';
		if (!empty($this->_action) && !in_array($this->_action, self::ACTIONS))
			throw new \UnexpectedValueException("Index {$this->_default_name()}: invalid action ('$action'). Expected values are ".implode(', ', array_map([$this, '_quote'], self::ACTIONS))." or empty.");

		switch ($action)
		{
			case SQLAction::DROP:
				if (!$this->_name)
					throw new \UnexpectedValueException("The index name must be set to drop it.");

				$this->sql = "$action INDEX {$this->parse_query_name()}";
				break;
			case SQLAction::ADD:
			default:
				$columns = $this->parse_query_fields();
				if (!$columns && $this->_action !== SQLAction::DROP)
					throw new \UnexpectedValueException("Index $name: The columns to which the index refers must be set to create it.");

				$comment = $this->parse_query_comment();
				$type = $this->parse_query_fulltext().$this->parse_query_spatial().$this->parse_query_unique();

				$this->sql = trim("{$action}{$type} INDEX {$this->parse_query_name()} ($columns)$comment");
				break;
		}

		return $this->sql;
	}

	/**
	 * Create a new INDEX query
	 *
	 * @return Index
	 */
	public static function create()
	{
		return new static();
	}
}