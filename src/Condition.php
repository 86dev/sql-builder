<?php
namespace SQLBuilder;
use SQLBuilder\Statements\Query;
use SQLBuilder\Statements\Select;
use SQLBuilder\Enums\ConditionType;
use SQLBuilder\Enums\ConditionRelation;
use SQLBuilder\Enums\ConditionOperation;
use SQLBuilder\Statements\Traits\TableTrait;
use SQLBuilder\Statements\Traits\AliasTrait;

/**
 * SQL Condition/Setter builder.
 *
 * @version 1.0
 * @author 86Dev
 */
class Condition extends Query
{
	use TableTrait;

	#region Variables
	/**
	 * Field name
	 *
	 * @var string
	 */
	protected $_field;

	/**
	 * Values
	 *
	 * @var array
	 */
	protected $_values;

	/**
	 * Operator
	 *
	 * @var string
	 */
	protected $_operator;

	/**
	 * Relation between values
	 *
	 * @var string
	 */
	protected $_relation;

	/**
	 * Values type
	 *
	 * @var string
	 */
	protected $_type;

	/**
	 * Determine if the query should enclosed field and table in backtick. Also apply to values when type is column
	 *
	 * @var bool
	 */
	protected $_do_not_use_backtick;
	#endregion

	#region Getters
	/**
	 * Get field
	 *
	 * @return string
	 */
	public function get_field()
	{
		return $this->_field;
	}

	/**
	 * Get table
	 *
	 * @return string
	 */
	public function get_table()
	{
		return $this->_table;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function get_type()
	{
		return $this->_type;
	}

	/**
	 * Get operator
	 *
	 * @return string
	 */
	public function get_operator()
	{
		return $this->_operator;
	}

	/**
	 * Get relation
	 *
	 * @return string
	 */
	public function get_relation()
	{
		return $this->_relation;
	}

	/**
	 * Get do_not_use_backtick
	 *
	 * @return bool
	 */
	public function get_do_not_use_backtick()
	{
		return $this->_do_not_use_backtick;
	}

	/**
	 * Get values
	 *
	 * @return array
	 */
	public function get_values()
	{
		return $this->_values;
	}
	#endregion

	#region Setters
	/**
	 * Set field name
	 *
	 * @param string $field
	 * @return Condition
	 */
	public function field($field)
	{
		$this->_field = $field;
		return $this;
	}

	/**
	 * Set values
	 *
	 * @param mixed $values
	 * @return Condition
	 */
	public function values($values)
	{
		if (!is_array($values))
			$values = [$values];
		$this->_values = $values;
		return $this;
	}

	/**
	 * Set table
	 *
	 * @param string $table
	 * @return Condition
	 */
	public function table($table)
	{
		$this->_table = $table;
		return $this;
	}

	/**
	 * Set relation name
	 *
	 * @param string $relation
	 * @return Condition
	 */
	public function relation($relation)
	{
		$this->_relation = strtoupper($relation);
		return $this;
	}

	/**
	 * Set operator name
	 *
	 * @param string $operator
	 * @return Condition
	 */
	public function operator($operator)
	{
		$this->_operator = strtoupper($operator);
		return $this;
	}

	/**
	 * Set type name
	 *
	 * @param string $type
	 * @return Condition
	 */
	public function type($type)
	{
		$this->_type = $type ? strtolower($type) : null;
		return $this;
	}

	/**
	 * Determine if the query should enclosed field and table in backtick. Also apply to values when type is column
	 *
	 * @param string $do_not_use_backtick
	 * @return Condition
	 */
	public function do_not_use_backtick($do_not_use_backtick = true)
	{
		$this->_do_not_use_backtick = $do_not_use_backtick;
		return $this;
	}
	#endregion

	/**
	 * Reset the query
	 *
	 * @return Condition
	 */
	public function new_query()
	{
		$this->new_query_table();
		$this->_field = null;
		$this->_values = [];
		$this->_type = null;
		$this->_operator = ConditionOperation::EQ;
		$this->_relation = ConditionRelation::_OR;
		$this->_do_not_use_backtick = false;
		return $this;
	}

	/**
	 * Get the condition SQL string
	 *
	 * @return string
	 */
	public function parse_query()
	{
		$field = $this->_field;
		if ($this->_table)
			$field = $this->_table.'.'.$field;
		if (!$this->_do_not_use_backtick)
			$field = SQL::backtick($field);

		if (in_array($this->_operator, [ConditionOperation::LIKE, ConditionOperation::NOTLIKE]))
			$this->_values = array_map(function($value) { return '\'%'.self::esc_like($value).'%\''; }, $this->_values);
		else
			$this->_values = array_map([Condition::class, 'prepare_value'], $this->_values, array_fill(0, count($this->_values), $this->_type));

		// Check if the number of values given is working with the given operator
		if (count($this->_values) === 0 || $this->_values === null)
		{
			if (in_array($this->_operator, ConditionOperation::NEGATIVES))
				$this->_operator = ConditionOperation::ISNOTNULL;
			else
				$this->_operator = ConditionOperation::ISNULL;
		}
		else if (count($this->_values) === 1)
		{
			if (in_array($this->_operator, [ConditionOperation::BETWEEN]))
				$this->_operator = ConditionOperation::EQ;
			else if (in_array($this->_operator, [ConditionOperation::NOTBETWEEN]))
				$this->_operator = ConditionOperation::NEQ;
		}

		$allow_relation = true;
		$format = '%s';
		switch ($this->_operator)
		{
			case ConditionOperation::ISNULL:
			case ConditionOperation::ISNOTNULL:
				$where = "$field $this->_operator";
				$allow_relation = false;
				break;
			case ConditionOperation::BETWEEN:
			case ConditionOperation::NOTBETWEEN:
				$where = sprintf("$field $this->_operator $format AND $format", $this->_values[0], $this->_values[1]);
				$allow_relation = false;
				break;
			case ConditionOperation::IN:
			case ConditionOperation::NOTIN:
				$values = join(', ', $this->_values);
				$where = "$field $this->_operator ($values)";
				$allow_relation = false;
				break;
			default:
				$where = "$field $this->_operator $format";
				break;
		}
		if ($allow_relation)
		{
			$operator = $this->_operator;
			$where = join(" $this->_relation ", array_map(function($value) use($field, $operator, $where) {
				if ($value === null)
					return $field.' '.(in_array($operator, ConditionOperation::NEGATIVES) ? ConditionOperation::ISNOTNULL : ConditionOperation::ISNULL);
				else
					return sprintf($where, $value);
			}, $this->_values));

			if (count($this->_values) > 1)
				$where = "($where)";
		}
		return $where;
	}

	/**
	 * Parse a text to be used in a LIKE statement
	 *
	 * @param string $text
	 * @return string
	 */
	public static function esc_like($text)
	{
		return addcslashes($text, '_%\\');
	}

	/**
	 * Prepare a value to be used in a SQL statement
	 *
	 * @param mixed $value
	 * @param string $type The value type, will be infered from real value type if null. See ConditionType for possible types.
	 * @return string The value converted to string
	 */
	public static function prepare_value($value, $type = null)
	{
		if ($value === null)
			return $value;

		if ($type === null)
		{
			if (is_bool($value))
				$type = ConditionType::BOOL;
			else if (is_int($value) || is_long($value))
				$type = ConditionType::INT;
			else if (is_double($value) || is_float($value))
				$type = ConditionType::DOUBLE;
			else if (is_string($value))
				$type = ConditionType::STRING;
			else if (is_a($value, \DateTime::class))
				$type = ConditionType::DATETIME;
			else if (is_a($value, Select::class))
				$type = ConditionType::SELECT;
		}

		switch ($type)
		{
			case ConditionType::DATE:
			case ConditionType::DATETIME:
			case ConditionType::TIME:
			case ConditionType::TIMESTAMP:
				$format = $type === ConditionType::DATE ? 'Y-m-d'
					: ($type === ConditionType::TIME ? 'H:i:sP'
					: 'c');

				if (is_a($value, \DateTime::class))
					$value = $value->format($format);
				else if (is_int($value))
					$value = date($format, $value);
				$value = SQL::quote($value);
				$type = '%s';
				break;
			case ConditionType::COLUMN:
				$value = SQL::backtick($value);
				$type = '%s';
				break;
			case ConditionType::SELECT:
				$value = $value->parse_query();
				$type = '(%s)';
				break;
			case ConditionType::BOOL:
			case ConditionType::BOOLEAN:
				$value = \PHPTools\BoolHelper::to_bool($value) ? "b'1'" : "b'0'";
				$type = '%s';
				break;
			case ConditionType::INT:
			case ConditionType::INTEGER:
			case ConditionType::LONG:
				$type = '%d';
				break;
			case ConditionType::DOUBLE:
			case ConditionType::FLOAT:
				$type = '%G';
				break;
			case ConditionType::STRING:
			default:
				$value = SQL::quote($value);
				$type = '%s';
				break;
		}
		return sprintf($type, $value);
	}

	/**
	 * Create a new condition
	 *
	 * @return Condition
	 */
	public static function create($field, $table = null, $do_not_use_backtick = false)
	{
		return (new Condition())
			->field($field)
			->table($table)
			->do_not_use_backtick($do_not_use_backtick);
	}

	/**
	 * Make a SQL condition
	 * Examples:
	 * ('name', 'a') => `name` = 'a'
	 * ('name', null) => `name` IS NULL
	 * ('name', ['a', 'b', 'c'], null, ConditionOperator::IN) => `name` IN ('a', 'b', 'c')
	 * ('parent_id', [null, 0]) => (`parent_id` IS NULL OR `parent_id` = 0)
	 * ('parent_id', [null, 0], ConditionType::INT|null, ConditionOperator::NEQ, ConditionRelation::_AND) => (`parent_id` IS NOT NULL AND `parent_id` != 0)
	 * ('valid', true) => `valid` = 1
	 * ('price', 12.99) => `price` = 12.99
	 * ('date', time(), ConditionType::DATETIME) => `date` = '2018-01-01T13:00:00+02:00'
	 * ('date', [strtotime('first day of this month'), strtotime('last day of this month')], ConditionType::DATE, ConditionOperator::BETWEEN) => `date` BETWEEN '2018-04-01' AND '2018-04-30'
	 * ('a.id', 'b.id', ConditionType::COLUMN) => `a`.`id` = `b`.`id`
	 *
	 * @param string $field The field name with alias if needed
	 * @param mixed $values The values to test.
	 *						Note about string fields: they will be slashed and quoted with SQLHelper::quote
	 *						Note about Date, DateTime and Time fields: if a DateTime object or an int is given, it will be converted to a valid SQL format ; if a string is given, it is assumed to be in a correct SQL format.
	 * @param string $table Table name or alias
	 * @param string $type The field type, see ConditionType for available types. If null, it will be defined from first value type. Be aware that when passing null with a value being an int timestamp, the int won't be converted to a valid SQL format.
	 * @param string $operator The operation to apply, see ConditionOperation for available operations
	 * @param string $relation The relation between each tests (one per value), either ConditionRelation::_AND or ConditionRelation::_OR
	 * @param bool $do_not_use_backtick Specifies that the field should not be surrounded with backtick. Usefull for function field like 'COUNT(*)'. If true, you should protect column name passed as argument to the function with SQL::backtick
	 * @return Condition
	 */
	public static function make($field, $values, $table = null, $type = null, $operator = ConditionOperation::EQ, $relation = ConditionRelation::_OR, $do_not_use_backtick = false)
	{
		return (new Condition($field, $table, $do_not_use_backtick))
			->values($values)
			->type($type)
			->operator($operator)
			->relation($relation);
	}

	/**
	 * Create a null condition like `col1` IS NULL
	 *
	 * @param string $field
	 * @param string $table
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function is_null($field, $table = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->operator(ConditionOperation::ISNULL);
	}

	/**
	 * Create a not null condition like `col1` IS NOT NULL
	 *
	 * @param string $field
	 * @param string $table
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function is_not_null($field, $table = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->operator(ConditionOperation::ISNOTNULL);
	}

	/**
	 * Create a like condition like `col1` LIKE '%test%'
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function like($field, $values, $table = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type(ConditionType::STRING)
			->operator(ConditionOperation::LIKE);
	}

	/**
	 * Create a not like condition like `col1` NOT LIKE '%test%'
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function not_like($field, $values, $table = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type(ConditionType::STRING)
			->operator(ConditionOperation::NOTLIKE);
	}

	/**
	 * Create an in condition like `col1` IN (0, 10, 20)
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function in($field, $values, $table = null, $type = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->operator(ConditionOperation::IN);
	}

	/**
	 * Create a not in condition like `col1` NOT IN (0, 10, 20)
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function not_in($field, $values, $table = null, $type = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->operator(ConditionOperation::NOTIN);
	}

	/**
	 * Create a between condition like `col1` BETWEEN 0 AND 10
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function between($field, $values, $table = null, $type = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->operator(ConditionOperation::BETWEEN);
	}

	/**
	 * Create a not between condition like `col1` NOT BETWEEN 0 AND 10
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function not_between($field, $values, $table = null, $type = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->operator(ConditionOperation::NOTBETWEEN);
	}

	/**
	 * Create an equal condition like `col1` = 0
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param string $relation
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function eq($field, $values, $table = null, $type = null, $relation = ConditionRelation::_OR, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->relation($relation)
			->operator(ConditionOperation::EQ);
	}

	/**
	 * Create a not equal condition like `col1` != 0
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param string $relation
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function neq($field, $values, $table = null, $type = null, $relation = ConditionRelation::_AND, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->relation($relation)
			->operator(ConditionOperation::NEQ);
	}

	/**
	 * Create a lesser than condition like `col1` < 0
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function lt($field, $values, $table = null, $type = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->operator(ConditionOperation::LT);
	}

	/**
	 * Create a lesser than or equal condition like `col1` <= 0
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function lte($field, $values, $table = null, $type = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->operator(ConditionOperation::LTE);
	}

	/**
	 * Create a greater than condition like `col1` > 0
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function gt($field, $values, $table = null, $type = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->operator(ConditionOperation::GT);
	}

	/**
	 * Create a greater than or equal condition like `col1` >= 0
	 *
	 * @param string $field
	 * @param mixed $values
	 * @param string $table
	 * @param string $type
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function gte($field, $values, $table = null, $type = null, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type($type)
			->operator(ConditionOperation::GTE);
	}

	/**
	 * Create a column condition like `col1` = `col2`
	 *
	 * @param string $field
	 * @param string $values
	 * @param string $table
	 * @param string $operator
	 * @param boolean $do_not_use_backtick
	 * @return Condition
	 */
	public static function column($field, $values, $table = null, $operator = ConditionOperation::EQ, $do_not_use_backtick = false)
	{
		return Condition::create($field, $table, $do_not_use_backtick)
			->values($values)
			->type(ConditionType::COLUMN)
			->operator($operator);
	}

	/**
	 * Create a condition from an array
	 *
	 * @param array $args Must have key field and values defined. Optional keys are type, operator, relation and do_not_use_backtick. Values is optional if operator is 'IS [NOT] NULL'
	 * @return Condition
	 */
	public static function fromArray($args)
	{
		$condition = new Condition();
		if (array_key_exists('field', $args)) $condition->field($args['field']);
		if (array_key_exists('values', $args)) $condition->values($args['values']);
		if (array_key_exists('table', $args)) $condition->table($args['table']);
		if (array_key_exists('type', $args)) $condition->type($args['type']);
		if (array_key_exists('operator', $args)) $condition->operator($args['operator']);
		if (array_key_exists('relation', $args)) $condition->relation($args['relation']);
		if (array_key_exists('do_not_use_backtick', $args)) $condition->do_not_use_backtick($args['do_not_use_backtick']);
		return $condition;
	}

}