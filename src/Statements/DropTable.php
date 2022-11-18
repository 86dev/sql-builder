<?php
namespace SQLBuilder\Statements;
use SQLBuilder\SQL;

/**
 * DROP TABLE query builder
 *
 * @version 1.0
 * @author 86Dev
 */
class DropTable extends Query
{
	use Traits\TableTrait;
	use Traits\IfNotExistsTrait;

	/**
	 * Set table name
	 *
	 * @param string $table
	 */
	public function table($table): self
	{
		$this->set_table($table);
		return $this;
	}

	/**
	 * Set if the query should stop if the element does not exist
	 *
	 * @param bool $ifexists
	 */
	public function ifexists($ifexists = true): self
	{
		$this->set_ifnotexists($ifexists);
		return $this;
	}

	/**
	 * Reset the query
	 */
	public function new_query(): self
	{
		$this->new_query_table();
		$this->new_query_ifnotexists();
		return $this;
	}

	/**
	 * Get the query string
	 *
	 * @return string
	 */
	public function parse_query()
	{
		return "DROP TABLE "
			.($this->_ifnotexists ? "IF EXISTS " : '')
			.$this->parse_query_table();
	}

	/**
	 * Create a new DROP TABLE statement
	 *
	 * @param string $table Table name
	 * @param boolean $ifexists Delete the table only if it exists
	 */
	public static function create($table, $ifexists = false): self
	{
		return (new static())->table($table)->ifexists($ifexists);
	}
}
