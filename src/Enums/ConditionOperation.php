<?php

namespace SQLBuilder\Enums;

/**
 * Condition available operations
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class ConditionOperation extends \PHPTools\Enum
{
	const EQ = '=';
	const NEQ = '!=';
	const GT = '>';
	const LT = '<';
	const LTE = '<=';
	const GTE = '>=';
	const IN = 'IN';
	const NOTIN = 'NOT IN';
	const BETWEEN = 'BETWEEN';
	const NOTBETWEEN = 'NOT BETWEEN';
	const LIKE = 'LIKE';
	const NOTLIKE = 'NOT LIKE';
	const ISNULL = 'IS NULL';
	const ISNOTNULL = 'IS NOT NULL';

	const NEGATIVES = [self::NEQ, self::ISNOTNULL, self::NOTBETWEEN, self::NOTIN, self::NOTLIKE];
}