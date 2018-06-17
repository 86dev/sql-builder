<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define CONSTRAINT properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait ConstraintTrait
{
	/**
	 * Index constraint
	 * @var \string
	 */
	protected $_constraint;

	/**
	 * Get index constraint
	 *
	 * @return \string
	 */
	public function get_constraint()
	{
		return $this->_constraint;
	}

	/**
	 * Set index constraint
	 *
	 * @param \string $constraint
	 */
	public abstract function constraint($constraint);

	/**
	 * Set index constraint
	 *
	 * @param \string $constraint
	 */
	protected function set_constraint($constraint)
	{
		$this->_constraint = $constraint;
	}

	/**
	 * Reset constraint query value
	 *
	 * @return void
	 */
	protected function new_query_constraint()
	{
		$this->_constraint = null;
	}

	/**
	 * Get constraint query string
	 *
	 * @return string
	 */
	protected function parse_query_constraint()
	{
		return $this->_constraint ? "ALGORITHM = $this->_constraint" : '';
	}

}
