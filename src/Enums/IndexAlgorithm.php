<?php

namespace SQLBuilder\Enums;

/**
 * Index available algorithm types
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class IndexAlgorithm extends \PHPTools\Enum
{
	const COPY = 'COPY';
	const _DEFAULT = 'DEFAULT';
	const INPLACE = 'INPLACE';
}