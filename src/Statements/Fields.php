<?php

namespace SQLBuilder\Statements;

/**
 * Fields builder
 *
 * @version 1.0
 * @author 86Dev
 */
class Fields extends Query
{
	use Traits\FieldsTrait;

	public function field($field, $table = '', $alias = '', $do_not_use_backtick = false)
	{
		$this->add_field($field, $table, $alias, $do_not_use_backtick);
		return $this;
	}

	public function fields($fields, $table = '')
	{
		$this->set_fields($fields, $table);
		return $this;
	}

	public function new_query()
	{
		$this->new_query_fields();
		return $this;
	}

	public function parse_query()
	{
		return $this->parse_query_fields();
	}

	public static function create()
	{
		return new static();
	}
}
