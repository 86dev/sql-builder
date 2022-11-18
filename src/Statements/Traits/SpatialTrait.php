<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define spatial properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait SpatialTrait
{
	/**
	 * Indicate whether the value must be spatial
	 *
	 * @var bool
	 */
	protected $_spatial;

	/**
	 * Indicate whether the value must be spatial
	 *
	 * @return bool
	 */
	public function get_spatial()
	{
		return $this->_spatial;
	}

	/**
	 * Set whether the value must be spatial
	 *
	 * @param bool $spatial
	 */
	protected function set_spatial($spatial = true)
	{
		$this->_spatial = $spatial;
	}

	/**
	 * Reset spatial query value
	 *
	 * @return void
	 */
	protected function new_query_spatial()
	{
		$this->_spatial = false;
	}

	/**
	 * Get spatial query string
	 *
	 * @return string
	 */
	protected function parse_query_spatial()
	{
		return $this->_spatial ? ' SPATIAL' : '';
	}
}
