<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define fulltext properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait FullTextTrait
{
	/**
	 * Indicate whether the value must be fulltext
	 *
	 * @var bool
	 */
	protected $_fulltext;

	/**
	 * Indicate whether the value must be fulltext
	 *
	 * @return bool
	 */
	public function get_fulltext()
	{
		return $this->_fulltext;
	}

	/**
	 * Set whether the value must be fulltext
	 *
	 * @param bool $fulltext
	 */
	protected function set_fulltext($fulltext = true)
	{
		$this->_fulltext = $fulltext;
	}

	/**
	 * Reset fulltext query value
	 *
	 * @return void
	 */
	protected function new_query_fulltext()
	{
		$this->_fulltext = false;
	}

	/**
	 * Get fulltext query string
	 *
	 * @return string
	 */
	protected function parse_query_fulltext()
	{
		return $this->_fulltext ? ' FULLTEXT' : '';
	}
}
