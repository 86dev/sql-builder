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

	/**
	 * Add a field to the query
	 *
	 * @param \string $field The field name
	 * @param \string $table The field's table name or alias (must be consistent with tables specified in the query)
	 * @param \string $alias The field alias
	 * @param \bool $do_not_use_backtick Specifies that the query builder should not surround this field with backtick. Usefull for functions field like 'COUNT(*)'. If true, you should prepare the field yourself by calling SQL::backtick on the function arguments who need it.
	 * @return Fields
	 */
	public function field($field, $table = '', $alias = '', $do_not_use_backtick = false)
	{
		$this->add_field($field, $table, $alias, $do_not_use_backtick);
		return $this;
	}

	/**
	 * Set a list of fields from the same table. Will replace existing field with the same alias, or field name if alias is not defined.
	 * Do not use this function to add function field like 'COUNT(*)' as it will be surrounded with backtick
	 *
	 * @param array $fields An array of field name with optional alias as key (['a', 'test' => 'b', ...])
	 * @param string $table Optional table name or alias
	 * @return Fields
	 */
	public function fields($fields, $table = '')
	{
		$this->set_fields($fields, $table);
		return $this;
	}

	/**
	 * Reset fields query value
	 *
	 * @return void
	 */
	public function new_query()
	{
		$this->new_query_fields();
		return $this;
	}

	/**
	 * Get fields query string
	 *
	 * @return string
	 */
	public function parse_query()
	{
		return $this->parse_query_fields();
	}

	/**
	 * Create a new Fields instance
	 *
	 * @return Fields
	 */
	public static function create()
	{
		return new static();
	}
}
