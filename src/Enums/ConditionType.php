<?php

namespace SQLBuilder\Enums;

/**
 * Condition available types
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class ConditionType extends \PHPTools\Enum
{
	const STRING = 'string';
	const DATE = 'date';
	const DATETIME = 'datetime';
	const TIME = 'time';
	const TIMESTAMP = 'timestamp';
	const INT = 'int';
	const INTEGER = 'integer';
	const FLOAT = 'float';
	const DOUBLE = 'double';
	const BOOL = 'bool';
	const BOOLEAN = 'boolean';
	const LONG = 'long';
	const COLUMN = 'column';
	const SELECT = 'select';
}