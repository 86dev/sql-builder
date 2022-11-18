<?php

namespace SQLBuilder\Statements\Traits;
use SQLBuilder\Enums\SQLAction;

/**
 * Define action properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait ActionTrait
{
	/**
	 * Action
	 * @var string
	 */
	protected $_action;

	/**
	 * Indicates if an empty action is allowed
	 *
	 * @var bool
	 */
	protected $_allow_empty_action = true;

	/**
	 * Get action
	 *
	 * @return string
	 */
	public function get_action()
	{
		return $this->_action;
	}

	/**
	 * Set action
	 *
	 * @param string $action
	 * @throws \UnexpectedValueException
	 */
	protected function set_action($action)
	{
		$this->check_action($action);
		$this->_action = $action;
	}

	/**
	 * Reset action query value
	 *
	 * @return void
	 */
	protected function new_query_action()
	{
		$this->_action = null;
	}

	/**
	 * Get action query string
	 *
	 * @return string
	 */
	protected function parse_query_action()
	{
		return $this->_action ?: '';
	}

	/**
	 * Check if action value is valid
	 *
	 * @param string $action
	 * @throws \UnexpectedValueException
	 * @return bool
	 */
	protected function check_action($action)
	{
		if((!empty($action) || !$this->_allow_empty_action) && !SQLAction::isValidValue($action))
		{
			$actions = defined('self::ACTIONS') ? self::ACTIONS : SQLAction::values();
			$actions = join(', ', array_map([$this, '_quote'], $actions));
			if (!$this->_allow_empty_action)
				$actions .= ' or empty';
			throw new \UnexpectedValueException("Invalid action ($action). Expected values are $actions.");
		}
		return true;
	}
}
