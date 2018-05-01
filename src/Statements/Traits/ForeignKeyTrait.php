<?php

namespace SQLBuilder\Statements\Traits;
use SQLBuilder\Enums\ForeignKeyAction;
use SQLBuilder\Enums\ForeignKeyMatch;

/**
 * Define foreign key properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait ForeignKeyTrait
{
	#region Variables
	/**
	 * Foreign key references table
	 * @var \string
	 */
	protected $_on;
	/**
	 * Foreign key references columns
	 * @var \string|\string[]
	 */
	protected $_references;
	/**
	 * Foreign key references delete action, see ForeignKeyAction for available values
	 * @var \string
	 */
	protected $_delete;
	/**
	 * Foreign key references update action, see ForeignKeyAction for available values
	 * @var \string
	 */
	protected $_update;
	/**
	 * Foreign Key match type, see ForeignKeyMatch for available values
	 * @var string
	 */
	protected $_match;
	#endregion

	#region Getters
	/**
	 * Get index references table
	 * @return \string
	 */
	public function get_on()
	{
		return $this->_on;
	}

	/**
	 * Get index references columns
	 * @return \string
	 */
	public function get_references()
	{
		return $this->_references;
	}

	/**
	 * Get index delete
	 * @return \string
	 */
	public function get_delete()
	{
		return $this->_delete;
	}

	/**
	 * Get index update
	 * @return \string
	 */
	public function get_update()
	{
		return $this->_update;
	}

	/**
	 * Get index match
	 * @return \string
	 */
	public function get_match()
	{
		return $this->_match;
	}
	#endregion

	#region Setters

	/**
	 * Set foreign key references table
	 * @param \string $on
	 */
	public abstract function on($on);

	/**
	 * Set foreign key references table
	 * @param \string $on
	 */
	protected function set_on($on)
	{
		$this->_on = $on;
		return $this;
	}

	/**
	 * Set foreign key references columns
	 * @param \string[] $references
	 */
	public abstract function references(...$references);

	/**
	 * Set foreign key references columns
	 * @param \string[] $references
	 */
	protected function set_references(...$references)
	{
		$this->_references = $references;
		return $this;
	}

	/**
	 * Set foreign key references delete action, see ForeignKeyAction for available values
	 * @param \string $delete
	 */
	public abstract function delete($delete);

	/**
	 * Set foreign key references delete action, see ForeignKeyAction for available values
	 * @param \string $delete
	 */
	protected function set_delete($delete)
	{
		if (!empty($delete) && !ForeignKeyAction::isValidValue($delete))
			throw new \UnexpectedValueException("Index {$this->_default_name()}: invalid delete action ('$delete'). Expected values are ".implode(', ', array_map([$this, '_quote'], ForeignKeyAction::values()))." or empty.");
		$this->_delete = $delete;
		return $this;
	}

	/**
	 * Set foreign key references update action, see ForeignKeyAction for available values
	 * @param \string $update
	 */
	public abstract function update($update);

	/**
	 * Set foreign key references update action, see ForeignKeyAction for available values
	 * @param \string $update
	 */
	protected function set_update($update)
	{
		if (!empty($update) && !ForeignKeyAction::isValidValue($update))
			throw new \UnexpectedValueException("Index {$this->_default_name()}: invalid update action ('$update'). Expected values are ".implode(', ', array_map([$this, '_quote'], ForeignKeyAction::values()))." or empty.");
		$this->_update = $update;
		return $this;
	}

	/**
	 * Set foreign key match, see ForeignKeyMatch for available values
	 * @param \string $match
	 */
	public abstract function match($match);

	/**
	 * Set foreign key match, see ForeignKeyMatch for available values
	 * @param \string $match
	 */
	protected function set_match($match)
	{
		if (!empty($match) && !ForeignKeyMatch::isValidValue($match))
			throw new \UnexpectedValueException("Index {$this->_default_name()}: invalid match ('$match'). Expected values are ".implode(', ', array_map([$this, '_quote'], ForeignKeyMatch::values()))." or empty.");
		$this->_match = $match;
		return $this;
	}
	#endregion

	protected function new_query_foreignkey()
	{
		$this->_on = null;
		$this->_references = null;
		$this->_match = null;
		$this->_delete = null;
		$this->_update = null;
	}

	protected function parse_query_foreignkey()
	{
		if (!$this->_on && !$this->_references)
			return '';

		if (!$this->_on)
			throw new \UnexpectedValueException("Index {$this->_default_name()}: the foreign table to which the index refers must be set.");
		if (!$this->_references)
			throw new \UnexpectedValueException("Index {$this->_default_name()}: the foreign columns to which the index refers must be set.");

		$references = null;
		if (is_array($this->_references))
			$references = implode(', ', array_map([$this, '_backtick'], $this->_references));
		else if (is_string($this->_references))
			$references = $this->_backtick($this->_references);
		$match = $this->_match ? " MATCH $this->_match" : '';
		$delete = $this->_delete ? " ON DELETE $this->_delete" : '';
		$update = $this->_update ? " ON UPDATE $this->_update" : '';
		return "REFERENCES {$this->_backtick($this->_on)}($references)$match$delete$update";
	}
}
