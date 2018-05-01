<?php
namespace SQLBuilder;
use SQLBuilder\Statements\Select;
use SQLBuilder\Enums\ConditionType;
use SQLBuilder\Enums\ConditionRelation;
use SQLBuilder\Enums\ConditionOperation;

/**
 * SQL Condition/Setter builder.
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class Condition
{
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
	 * @param \string $field The field name with alias if needed
	 * @param mixed $values The values to test.
	 *						Note about string fields: they will be slashed and quoted with SQLHelper::quote
	 *						Note about Date, DateTime and Time fields: if a DateTime object or an int is given, it will be converted to a valid SQL format ; if a string is given, it is assumed to be in a correct SQL format.
	 * @param \string $type The field type, see ConditionType for available types. If null, it will be defined from first value type. Be aware that when passing null with a value being an int timestamp, the int won't be converted to a valid SQL format.
	 * @param \string $operator The operation to apply, see ConditionOperation for available operations
	 * @param \string $relation The relation between each tests (one per value), either ConditionRelation::_AND or ConditionRelation::_OR
	 * @param \bool $do_not_use_backtick Specifies that the field should not be surrounded with backtick. Usefull for function field like 'COUNT(*)'. If true, you should protect column name passed as argument to the function with SQL::backtick
	 * @return \string
	 */
	public static function make($field, $values, $type = null, $operator = ConditionOperation::EQ, $relation = ConditionRelation::_OR, $do_not_use_backtick = false)
	{
		if (!$do_not_use_backtick)
			$field = SQL::backtick($field);
		if (!is_array($values))
			$values = [$values];

		if (in_array($operator, [ConditionOperation::LIKE, ConditionOperation::NOTLIKE]))
			$values = array_map(function($value) { return '\'%'.self::esc_like($value).'%\''; }, $values);
		else
			$values = array_map([Condition::class, 'prepare_value'], $values, array_fill(0, count($values), $type));

		// if ($type === null)
		// {
		// 	$value = $values[0];
		// 	if (is_null($value) && count($values) > 1)
		// 		$value = $values[1];

		// 	if (is_int($value) || is_long($value))
		// 		$type = ConditionType::INT;
		// 	else if (is_double($value) || is_float($value))
		// 		$type = ConditionType::DOUBLE;
		// 	else if (is_bool($value))
		// 		$type = ConditionType::BOOL;
		// 	else if (is_string($value))
		// 		$type = ConditionType::STRING;
		// 	else if (is_a($value, \DateTime::class))
		// 		$type = ConditionType::DATETIME;
		// 	else if (is_a($value, Select::class))
		// 		$type = ConditionType::SELECT;
		// }

		// switch ($type)
		// {
		// 	case ConditionType::DATE:
		// 	case ConditionType::DATETIME:
		// 	case ConditionType::TIME:
		// 	case ConditionType::TIMESTAMP:
		// 		$format = $type === ConditionType::DATE ? 'Y-m-d'
		// 			: ($type === ConditionType::TIME ? 'H:i:sP'
		// 			: 'c');

		// 		array_walk($values, function(&$value) use ($format) {
		// 			if (is_a($value, \DateTime::class))
		// 				$value = $value->format($format);
		// 			else if (is_int($value))
		// 				$value = date($format, $value);
		// 		});
		// 		$values = array_map([SQL::class, 'quote'], $values);
		// 		$type = '%s';
		// 		break;
		// 	case ConditionType::STRING:
		// 		if (!in_array($operator, [ConditionOperation::LIKE, ConditionOperation::NOTLIKE]))
		// 			$values = array_map([SQL::class, 'quote'], $values);
		// 		$type = '%s';
		// 		break;
		// 	case ConditionType::COLUMN:
		// 		$values = array_map([SQL::class, 'backtick'], $values);
		// 		$type = '%s';
		// 		break;
		// 	case ConditionType::SELECT:
		// 		$type = '%s';
		// 		break;
		// 	case ConditionType::BOOL:
		// 	case ConditionType::BOOLEAN:
		// 		$values = array_map(function($b) { return \PHPTools\BoolHelper::to_bool($b) ? 1 : 0; }, $values);
		// 	case ConditionType::INT:
		// 	case ConditionType::INTEGER:
		// 	case ConditionType::LONG:
		// 		$type = '%d';
		// 		break;
		// 	case ConditionType::DOUBLE:
		// 	case ConditionType::FLOAT:
		// 		$type = '%G';
		// 		break;
		// }
		$operator = strtoupper($operator);

		//check if the number of values given is working with the given operator
		if (count($values) === 0 || $values === null)
		{
			if (in_array($operator, [ConditionOperation::NEQ, ConditionOperation::NOTBETWEEN, ConditionOperation::NOTLIKE, ConditionOperation::NOTIN]))
				$operator = ConditionOperation::ISNOTNULL;
			else
				$operator = ConditionOperation::ISNULL;
		}
		else if (count($values) === 1)
		{
			if (in_array($operator, [ConditionOperation::BETWEEN]))
				$operator = ConditionOperation::EQ;
			else if (in_array($operator, [ConditionOperation::NOTBETWEEN]))
				$operator = ConditionOperation::NEQ;
		}

		$allow_relation = true;
		$type = '%s';
		switch ($operator)
		{
			case ConditionOperation::ISNULL:
			case ConditionOperation::ISNOTNULL:
				$where = "$field $operator";
				break;
			case ConditionOperation::BETWEEN:
			case ConditionOperation::NOTBETWEEN:
				$where = sprintf("$field $operator $type AND $type", $values[0], $values[1]);
				$allow_relation = false;
				break;
			case ConditionOperation::IN:
			case ConditionOperation::NOTIN:
				$values = join(', ', $values);
				$where = "$field $operator ($values)";
				$allow_relation = false;
				break;
			// case ConditionOperation::LIKE:
			// case ConditionOperation::NOTLIKE:
			// 	$type = "'%s'";
			// 	$values = array_map(function($value) { return '%'.self::esc_like($value).'%'; }, $values);
			// 	$where = "$field $operator $type";
			// 	break;
			default:
				$where = "$field $operator $type";
				break;
		}
		if ($allow_relation)
		{
			$where = join(" $relation ", array_map(function($value) use($field, $operator, $where) {
				if ($value === null)
					return $field.' '.($operator === '!=' || stripos($operator, 'NOT') !== false ? ConditionOperation::ISNOTNULL : ConditionOperation::ISNULL);
				// else if (is_a($value, Select::class))
				// 	return sprintf($where, '('.$value->parse_query().')');
				else
					return sprintf($where, $value);
			}, $values));

			if (count($values) > 1)
				$where = "($where)";
		}
		return $where;
	}

	/**
	 * Parse a text to be used in a LIKE statement
	 *
	 * @param \string $text
	 * @return \string
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
				$type = '(%s)';
				break;
			case ConditionType::BOOL:
			case ConditionType::BOOLEAN:
				$value = \PHPTools\BoolHelper::to_bool($value) ? "b'1'" : "b'0'";
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

	public static function is_null($field, $do_not_use_backtick = false)
	{
		return Condition::make($field, null, ConditionType::STRING, ConditionOperation::ISNULL, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function is_not_null($field, $do_not_use_backtick = false)
	{
		return Condition::make($field, null, ConditionType::STRING, ConditionOperation::ISNOTNULL, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function like($field, $values, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, ConditionType::STRING, ConditionOperation::LIKE, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function not_like($field, $values, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, ConditionType::STRING, ConditionOperation::NOTLIKE, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function in($field, $values, $type = null, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::IN, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function not_in($field, $values, $type = null, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::NOTIN, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function between($field, $values, $type = null, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::BETWEEN, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function not_between($field, $values, $type = null, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::NOTBETWEEN, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function eq($field, $values, $type = null, $relation = ConditionRelation::_OR, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::EQ, $relation, $do_not_use_backtick);
	}

	public static function neq($field, $values, $type = null, $relation = ConditionRelation::_AND, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::NEQ, $relation, $do_not_use_backtick);
	}

	public static function lt($field, $values, $type = null, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::LT, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function lte($field, $values, $type = null, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::LTE, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function gt($field, $values, $type = null, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::GT, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function gte($field, $values, $type = null, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, $type, ConditionOperation::GTE, conditionRelation::_OR, $do_not_use_backtick);
	}

	public static function column($field, $values, $operator = ConditionOperation::EQ, $do_not_use_backtick = false)
	{
		return Condition::make($field, $values, ConditionType::COLUMN, $operator, conditionRelation::_OR, $do_not_use_backtick);
	}

}