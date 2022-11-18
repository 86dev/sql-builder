<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define charset properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait CharsetTrait
{
	/**
	 * Charset
	 *
	 * @var string
	 */
	protected $_charset;

	/**
	 * Get charset
	 *
	 * @return string
	 */
	public function get_charset()
	{
		return $this->_charset;
	}

	/**
	 * Set charset
	 *
	 * @param string $charset
	 */
	protected function set_charset($charset)
	{
		$this->_charset = $charset;
	}

	/**
	 * Reset cherset query value
	 *
	 * @return void
	 */
	protected function new_query_charset()
	{
		$this->_charset = null;
	}

	/**
	 * Get charset query string
	 *
	 * @return string
	 */
	protected function parse_query_charset()
	{
		return $this->_charset ? "CHARSET $this->_charset" : '';
	}

}
