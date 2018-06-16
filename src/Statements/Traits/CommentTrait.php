<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define comment properties and behavior
 *
 * @version 1.0
 * @author 86Dev
 */
trait CommentTrait
{
	/**
	 * Index comment
	 * @var \string
	 */
	protected $_comment;

	/**
	 * Get index comment
	 *
	 * @return \string
	 */
	public function get_comment()
	{
		return $this->_comment;
	}

	/**
	 * Set index comment
	 *
	 * @param \string $comment
	 */
	protected function set_comment($comment)
	{
		$this->_comment = $comment;
	}

	/**
	 * Reset comment query value
	 *
	 * @return void
	 */
	protected function new_query_comment()
	{
		$this->_comment = null;
	}

	/**
	 * Get comment query string
	 *
	 * @return string
	 */
	protected function parse_query_comment()
	{
		return $this->_comment ? "COMMENT {$this->_quote($this->_comment)}" : '';
	}
}
