<?php

namespace SQLBuilder\Statements\Traits;
use SQLBuilder\Enums\IndexLock;

/**
 * Define lock properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait LockTrait
{
	/**
	 * Index lock, see IndexLock for available values
	 *
	 * @var \string
	 */
	protected $_lock;

	/**
	 * Get index lock
	 *
	 * @return \string
	 */
	public function get_lock()
	{
		return $this->_lock;
	}

	/**
	 * Set index lock, see IndexLock for available values
	 *
	 * @param \string $lock
	 */
	protected function set_lock($lock)
	{
		if (!empty($lock) && !IndexLock::isValidValue($lock))
			throw new \UnexpectedValueException("Index {$this->_default_name()}: invalid lock ('$lock'). Expected values are ".implode(', ', array_map([$this, '_quote'], IndexLock::values()))." or empty.");
		$this->_lock = $lock;
	}

	/**
	 * Reset lock query value
	 *
	 * @return void
	 */
	protected function new_query_lock()
	{
		$this->_lock = null;
	}

	/**
	 * Get lock query string
	 *
	 * @return string
	 */
	protected function parse_query_lock()
	{
		return $this->_lock ? "LOCK = $this->_lock" : '';
	}
}
