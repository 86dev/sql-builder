<?php

namespace SQLBuilder;
use SQLBuilder\Enums\SQLAction;
use SQLBuilder\ForeignKeyAction;

/**
 * Foreign key query builder
 *
 * @version 1.0
 * @author 86Dev
 */
class ForeignKey extends Statements\Query
{
	use Statements\Traits\IndexBasicTrait;
	use Statements\Traits\ForeignKeyTrait;

	const ACTIONS = [SQLAction::ADD, SQLAction::DROP];

	public function __construct($columns = null, $action = null)
	{
		parent::__construct();
		$this->action($action);
		if ($columns)
			$this->fields(...$columns);
	}

	/**
	 * Set foreign key name
	 *
	 * @param string $name
	 * @return ForeignKey
	 */
	public function name($name)
	{
		$this->set_name($name);
		return $this;
	}

	/**
	 * Set foreign key table
	 *
	 * @param string $table
	 * @return ForeignKey
	 */
	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	/**
	 * Set foreign key action
	 *
	 * @param string $action
	 * @return ForeignKey
	 */
	public function action($action)
	{
		$this->set_action($action);
		return $this;
	}

	/**
	 * Set foreign key type
	 *
	 * @param string $type
	 * @return ForeignKey
	 */
	public function type($type)
	{
		$this->set_type($type);
		return $this;
	}

	/**
	 * Set foreign key commentary
	 *
	 * @param string $comment
	 * @return FoerignKey
	 */
	public function comment($comment)
	{
		$this->set_comment($comment);
		return $this;
	}

	/**
	 * Set foreign key field
	 *
	 * @param string $field
	 * @param string $table
	 * @param string $alias
	 * @return ForeignKey
	 */
	public function field($field, $table = '', $alias = '')
	{
		$this->add_field($field, $table, $alias);
		return $this;
	}

	/**
	 * Set foreign key fields
	 *
	 * @param string ...$fields
	 * @return ForeignKey
	 */
	public function fields(...$fields)
	{
		$this->set_fields($fields);
		return $this;
	}

	/**
	 * Set foreign key reference table
	 *
	 * @param string $on
	 * @return ForeignKey
	 */
	public function on($on)
	{
		$this->set_on($on);
		return $this;
	}

	/**
	 * Set foreign key references columns
	 *
	 * @param string $references
	 * @return ForeignKey
	 */
	public function references(...$references)
	{
		$this->set_references(...$references);
		return $this;
	}

	/**
	 * Set foreign key delete action
	 *
	 * @param string $delete
	 * @return ForeignKey
	 */
	public function delete($delete)
	{
		$this->set_delete($delete);
		return $this;
	}

	/**
	 * Set foreign key update action
	 *
	 * @param string $update
	 * @return ForeignKey
	 */
	public function update($update)
	{
		$this->set_update($update);
		return $this;
	}

	/**
	 * Set foreign match option
	 *
	 * @param string $match
	 * @return ForeignKey
	 */
	public function match($match)
	{
		$this->set_match($match);
		return $this;
	}

	/**
	 * Reset the query
	 *
	 * @return ForeignKey
	 */
	public function new_query()
	{
		$this->new_query_basicindex();
		$this->new_query_foreignkey();
	}

	/**
	 * Get the query SQL
	 *
	 * @return string
	 */
	public function parse_query()
	{
		if ($this->_action === SQLAction::DROP)
			return "DROP FOREIGN KEY ".$this->parse_query_name();
		return trim("{$this->parse_query_action()} CONSTRAINT {$this->parse_query_name()} FOREIGN KEY ({$this->parse_query_fields()}) {$this->parse_query_foreignkey()}");
	}

	/**
	 * Get a default name for the foreign key
	 *
	 * @return string
	 */
	protected function _default_name()
	{
		return $this->_name ?: join('_', array_merge([$this->_table], array_map(function($field) { return $field['field']; }, $this->_fields), ['foreign_key']));
	}

	/**
	 * Create a new FOREIGN KEY statement
	 *
	 * @return ForeignKey
	 */
	public static function create()
	{
		return new static();
	}
}