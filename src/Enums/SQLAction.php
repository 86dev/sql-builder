<?php

namespace SQLBuilder\Enums;

/**
 * Index available actions
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class SQLAction extends \PHPTools\Enum
{
	const ADD = 'ADD';
	const CHANGE = 'CHANGE';
	const CREATE = 'CREATE';
	const DELETE = 'DELETE';
	const DROP = 'DROP';
	const INSERT = 'INSERT INTO';
	const MODIFY = 'MODIFY';
	const RENAME = 'RENAME';
	const SELECT = 'SELECT';
}