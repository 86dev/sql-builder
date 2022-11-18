<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define ifnotexists properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait IfNotExistsTrait
{
	/**
	 * Indicates if the query should stop if the element already exists
	 *
	 * @var bool
	 */
	protected $_ifnotexists;

	/**
	 * Get if the query should stop if the element already exists
	 *
	 * @return bool
	 */
	public function get_ifnotexists()
	{
		return $this->_ifnotexists;
	}

	/**
	 * Set if the query should stop if the element already exists
	 *
	 * @param bool $ifnotexists
	 */
	protected function set_ifnotexists($ifnotexists = true)
	{
		$this->_ifnotexists = $ifnotexists;
	}

	/**
	 * Reset ifnotexists query value
	 *
	 * @return void
	 */
	protected function new_query_ifnotexists()
	{
		$this->_ifnotexists = false;
	}

	/**
	 * Get ifnotexists query string
	 *
	 * @return string
	 */
	protected function parse_query_ifnotexists()
	{
		return $this->_ifnotexists ? " IF NOT EXISTS" : '';
	}

}
