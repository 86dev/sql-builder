<?php
namespace SQLBuilder\Statements;
use \SQLBuilder\Column;
use \SQLBuilder\Index;

/**
 * Command statement builder
 *
 * @version 1.0
 * @author 86Dev
 * @deprecated 1.0.4
 */
class Command extends Query
{
	#region Constants
	const ACTIONS = [self::ACTION_ALTER, self::ACTION_CREATE, self::ACTION_DROP, self::ACTION_RENAME];

	const TYPE_TABLE = 'TABLE';
	const TYPE_VIEW = 'VIEW';
	const TYPE_INDEX = 'INDEX';
	const TYPES = [self::TYPE_INDEX, self::TYPE_TABLE, self::TYPE_VIEW];
	#endregion

	#region Variables
	protected $_action;
	protected $_type;
	protected $_name;
	protected $_columns;
	protected $_indexes;
	#endregion

	public $sql;

	/**
	 * Reset the query
	 * @return SQL_CreateTable
	 */
	public function new_query()
	{
		$this->_table = '';
		$this->_columns = [];
		$this->_indexes = [];
		return $this;
	}

	/**
	 * Get the query SQL
	 * @return string
	 */
	public function parse_query()
	{
		$nl = $this->pretty_print ? "\n" : '';
		$nlt = $this->pretty_print ? "\n\t" : '';

		switch ($this->_action)
		{
			default:
		}

		$this->sql = "CREATE TABLE $this->_table (".$nlt
			.($this->_columns ? implode(', '.$nlt, array_map(function($column) { return $column->parse_query(); }, $this->_columns)) : '')
			.($this->_columns && $this->_indexes ? ','.$nlt : '')
			.($this->_indexes ? implode(', '.$nlt, $this->_indexes) : '')
			.$nl.')';

		return $this->sql;
	}

	#region Definition
	/**
	 * Command name
	 * @var string
	 */
	public function name($name)
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * Command new name
	 * Use only with RENAME action
	 * @var string
	 */
	public function new_name($new_name)
	{
		$this->_new_name = $new_name;
		return $this;
	}

	/**
	 * Command type, see Command::TYPE_* for available values
	 * @var string
	 */
	public function type($type)
	{
		if (!in_array($type, self::TYPES))
			throw new \UnexpectedValueException("Column {$this->_default_name()}: invalid type ('$type'). Expected values are ".implode(', ', array_map([$this, '_quote'], self::TYPES)));
		$this->_type = $type;
		return $this;
	}

	/**
	 * Command action, see Command::ACTION_* for available values
	 * @var string
	 */
	public function action($action)
	{
		if (!in_array($action, self::ACTIONS))
			throw new \UnexpectedValueException("Column {$this->_default_name()}: invalid action ('$action'). Expected values are ".implode(', ', array_map([$this, '_quote'], self::ACTIONS)));
		$this->_action = $action;
		return $this;
	}

	/**
	 * Add a column definition to the command
	 * @param Column $column
	 */
	public function column(Column $column)
	{
		$this->_columns[] = $column;
		return $this;
	}

	/**
	 * Add an index definition to the table
	 * @param Index $index
	 */
	public function index(Index $index)
	{
		$this->_indexes[] = $index;
		return $this;
	}
	#endregion

	public static function create()
	{
		return new static();
	}

}