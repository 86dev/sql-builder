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
	 * @var \string
	 */
	protected $_charset;

	/**
	 * Get charset
	 * @return \string
	 */
	public function get_charset()
	{
		return $this->_charset;
	}

	/**
	 * Set charset
	 * @param \string $charset
	 */
	public abstract function charset($charset);

	/**
	 * Set charset
	 * @param \string $charset
	 */
	protected function set_charset($charset)
	{
		$this->_charset = $charset;
		return $this;
	}

	protected function new_query_charset()
	{
		$this->_charset = null;
	}

	protected function parse_query_charset()
	{
		return $this->_charset ? "CHARSET $this->_charset" : '';
	}

}
