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
	 * @return \string
	 */
	public function get_comment()
	{
		return $this->_comment;
	}

	/**
	 * Set index comment
	 * @param \string $comment
	 */
	public abstract function comment($comment);

	/**
	 * Set index comment
	 * @param \string $comment
	 */
	protected function set_comment($comment)
	{
		$this->_comment = $comment;
		return $this;
	}

	protected function new_query_comment()
	{
		$this->_comment = null;
	}

	protected function parse_query_comment()
	{
		return $this->_comment ? "COMMENT {$this->_quote($this->_comment)}" : '';
	}
}
