<?php

namespace SQLBuilder\Enums;

/**
 * Index available lock types
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class IndexLock extends \PHPTools\Enum
{
	const _DEFAULT = 'DEFAULT';
	const EXCLUSIVE = 'EXCLUSIVE';
	const NONE = 'NONE';
	const SHARED = 'SHARED';
}