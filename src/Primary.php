<?php
namespace SQLBuilder;

use SQLBuilder\Enums\SQLAction;

/**
 * Primary key query builder
 *
 * @version 1.0
 * @author 86Dev
 */
class Primary extends Statements\Query
{
	use Statements\Traits\IndexBasicTrait;

	const ACTIONS = [SQLAction::ADD, SQLAction::DROP];

	/**
	 * Constructor allowing to specify columns and action
	 * @param string[] $columns
	 * @param string $action
	 */
	public function __construct($columns = null, $action = '')
	{
		parent::__construct();
		$this->_allow_empty_action = true;
		$this->action($action);
		if ($columns)
			$this->fields(...$columns);
	}

	/**
	 * Set index name
	 *
	 * @param string $name
	 * @return Primary
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
	 * @return Primary
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
	 * @return Primary
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
	 * @return Primary
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
	 * @return Primary
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
	 * @return Primary
	 */
	public function fields(...$fields)
	{
		$this->set_fields($fields);
		return $this;
	}

	/**
	 * Reset the query
	 *
	 * @return Primary
	 */
	public function new_query()
	{
		$this->new_query_basicindex();
		return $this;
	}

	/**
	 * Get the query SQL
	 *
	 * @return string
	 */
	public function parse_query()
	{
		$sql = '';

		$action = $this->parse_query_action();
		if (!empty($this->_action) && !in_array($this->_action, self::ACTIONS))
			throw new \UnexpectedValueException("Primary Key: invalid action ('$action'). Expected values are ".implode(', ', array_map([$this, '_quote'], self::ACTIONS))." or empty.");

		switch ($action)
		{
			case SQLAction::DROP:
				$sql = "$action PRIMARY KEY";
				break;
			case SQLAction::ADD:
			default:
				$columns = $this->parse_query_fields();
				if (!$columns)
					throw new \UnexpectedValueException("Primary Key: The columns to which the primary key refers must be set to create it.");

				$comment = $this->parse_query_comment();
				$sql = "$action PRIMARY KEY ($columns) $comment";
				break;

		}

		return trim($sql);
	}

	/**
	 * Create a new PRIMARY query
	 *
	 * @return Primary
	 */
	public static function create()
	{
		return new static();
	}
}