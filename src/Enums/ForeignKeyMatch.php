<?php

namespace SQLBuilder\Enums;

/**
 * Foreign key available match types
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class ForeignKeyMatch extends \PHPTools\Enum
{
	const FULL = 'FULL';
	const PARTIAL = 'PARTIAL';
	const SIMPLE = 'SIMPLE';
}