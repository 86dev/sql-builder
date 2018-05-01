<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define basic index behovior and components
 *
 * @version 1.0
 * @author 86Dev
 */
trait IndexBasicTrait
{
	use ActionTrait;
	use NameTrait;
	use TableTrait;
	use FieldsTrait;
	use CommentTrait;

	/**
	 * Reset the query
	 *
	 * @return static
	 */
	protected function new_query_basicindex()
	{
		$this->new_query_action();
		$this->new_query_name();
		$this->new_query_table();
		$this->new_query_fields();
		$this->new_query_comment();
	}

	protected function _default_name()
	{
		return $this->_name ?: join('_', array_merge([$this->_table], $this->_fields, ['index']));
	}
}
