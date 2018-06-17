<?php

namespace SQLBuilder\Statements\Traits;
use SQLBuilder\SQL;

/**
 * Define fields properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait FieldsTrait
{
	/**
	 * Fields
	 *
	 * @var \string[]
	 */
	protected $_fields;

	/**
	 * Get fields
	 *
	 * @return \string[]
	 */
	public function get_fields()
	{
		return $this->_fields;
	}

	/**
	 * Add a field to the query
	 *
	 * @param \string $field The field name
	 * @param \string $table The field's table name or alias (must be consistent with tables specified in the query)
	 * @param \string $alias The field alias
	 * @param \bool $do_not_use_backtick Specifies that the query builder should not surround this field with backtick. Usefull for functions field like 'COUNT(*)'. If true, you should prepare the field yourself by calling SQL::backtick on the function arguments who need it.
	 */
	protected function add_field($field, $table = '', $alias = '', $do_not_use_backtick = false)
	{
		if (is_array($field))
		{
			return $this->fields($field);
		}

		$this->_fields[$alias ?: ($table ? "$table." : '').$field] = ['field' => $field, 'table' => $table, 'alias' => $alias, 'do_not_use_backtick' => $do_not_use_backtick];
	}

	/**
	 * Set a list of fields from the same table. Will replace existing field with the same alias, or field name if alias is not defined.
	 * Do not use this function to add function field like 'COUNT(*)' as it will be surrounded with backtick
	 *
	 * @param array $fields An array of field name with optional alias as key (['a', 'test' => 'b', ...])
	 * @param string $table Optional table name or alias
	 */
	protected function set_fields($fields, $table = '')
	{
		foreach ($fields as $alias => $field)
		{
			$this->add_field($field, $table, is_string($alias) ? $alias : '');
		}
	}

	/**
	 * Reset fields query value
	 *
	 * @return void
	 */
	protected function new_query_fields()
	{
		$this->_fields = [];
	}

	/**
	 * Get fields query string
	 *
	 * @return string
	 */
	protected function parse_query_fields()
	{
		$fields = array_map(function($field_def) {
			extract($field_def);
			if (!$do_not_use_backtick)
			{
				$field = SQL::backtick($field);
				$table = SQL::backtick($table);
				$alias = SQL::backtick($alias);
			}
			$table = !empty($table) ? "$table." : '';
			$alias = !empty($alias) ? " AS $alias" : '';
			return $table.$field.$alias;
		}, $this->_fields);

		return implode(', ', $fields);
	}
}
