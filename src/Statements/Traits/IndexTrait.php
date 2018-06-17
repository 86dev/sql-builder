<?php

namespace SQLBuilder\Statements\Traits;

/**
 * Define basic index behovior and components
 *
 * @version 1.0
 * @author 86Dev
 */
trait IndexTrait
{
	use IndexBasicTrait;
	use UniqueTrait;
	use FullTextTrait;
	use SpatialTrait;

	/**
	 * Reset index query value
	 *
	 * @return void
	 */
	protected function new_query_index()
	{
		$this->new_query_basicindex();
		$this->new_query_fulltext();
		$this->new_query_spatial();
		$this->new_query_unique();
	}

	/**
	 * Set whether the value must be fulltext
	 *
	 * @param \bool $fulltext
	 */
	protected function set_fulltext($fulltext = true)
	{
		$this->_fulltext = $fulltext;
		if ($fulltext)
		{
			$this->_spatial = false;
			$this->_unique = false;
		}
	}

	/**
	 * Set whether the value must be spatial
	 *
	 * @param \bool $spatial
	 */
	protected function set_spatial($spatial = true)
	{
		$this->_spatial = $spatial;
		if ($spatial)
		{
			$this->_fulltext = false;
			$this->_unique = false;
		}
	}

	/**
	 * Set whether the value must be unique
	 *
	 * @param \bool $unique
	 */
	protected function set_unique($unique = true)
	{
		$this->_unique = $unique;
		if ($unique)
		{
			$this->_fulltext = false;
			$this->_spatial = false;
		}
	}

	/**
	 * Get index default name
	 *
	 * @return string
	 */
	protected function _default_name()
	{
		$type = $this->_unique ? 'unique' : ($this->_fulltext ? 'fulltext' : ($this->_spatial ? 'spatial' : 'index'));
		return $this->_name ?: join('_', array_merge([$this->_table], array_map(function($field) { return $field['field']; }, $this->_fields), [$type]));
	}
}
