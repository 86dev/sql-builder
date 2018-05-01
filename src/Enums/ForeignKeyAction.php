<?php

namespace SQLBuilder\Enums;

/**
 * Foreign key available actions on delete or update
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class ForeignKeyAction extends \PHPTools\Enum
{
	const CASCADE = 'CASCADE';
	const NOACTION = 'NO ACTION';
	const RESTRICT = 'RESTRICT';
	const SETDEFAULT = 'SET DEFAULT';
	const SETNULL = 'SET NULL';
}