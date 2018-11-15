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
	 * @param \string $table
	 * @return DropTable
	 */
	public function table($table)
	{
		$this->set_table($table);
		return $this;
	}

	/**
	 * Set if the query should stop if the element does not exist
	 *
	 * @param \bool $ifexists
	 * @return DropTable
	 */
	public function ifexists($ifexists = true)
	{
		$this->set_ifnotexists($ifexists);
		return $this;
	}

	/**
	 * Reset the query
	 *
	 * @return DropTable
	 */
	public function new_query()
	{
		$this->new_query_table();
		$this->new_query_ifnotexists();
	}

	/**
	 * Get the query string
	 *
	 * @return \string
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
	 * @return CreateTable
	 */
	public static function create($table, $ifexists = false)
	{
		return (new static())->table($table)->ifexists($ifexists);
	}
}
