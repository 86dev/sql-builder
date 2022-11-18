<?php
namespace SQLBuilder\Statements;

use SQLBuilder\Index;
use SQLBuilder\Column;
use SQLBuilder\Primary;
use SQLBuilder\ForeignKey;
use SQLBuilder\Statements\Query;

/**
 * CREATE TABLE statement
 *
 * @version 1.0
 * @author 86Dev
 */
class CreateTable extends Query
{
	use Traits\CharsetTrait;
	use Traits\CollateTrait;
	use Traits\IfNotExistsTrait;
	use Traits\ColumnsHelperTrait;
	use Traits\NameTrait;

	#region Variables
	/**
	 * Columns definitions
	 *
	 * @var Column[]
	 */
	protected $_columns;

	/**
	 * Indexes definitions
	 *
	 * @var Index[]
	 */
	protected $_indexes;
	#endregion

	#region Getters
	/**
	 * Get columns definitions
	 *
	 * @return Column[]
	 */
	public function get_columns()
	{
		return $this->_columns;
	}

	/**
	 * Get indexes definitions
	 *
	 * @return Index[]
	 */
	public function get_indexes()
	{
		return $this->_indexes;
	}
	#endregion

	#region Setters
	/**
	 * Add a column
	 *
	 * @param Column $column
	 */
	public function add_column(Column $column): self
	{
		$column->action(null);
		$this->_columns[] = $column;
		return $this;
	}

	/**
	 * Add an index
	 *
	 * @param Index|Primary|ForeignKey $index
	 */
	public function add_index($index): self
	{
		$index->action(null);
		$index->table($this->_name);
		$this->_indexes[] = $index;
		return $this;
	}

	/**
	 * Table name
	 *
	 * @param string $name
	 */
	public function name($name): self
	{
		$this->set_name($name);
		return $this;
	}

	/**
	 * Table charset
	 *
	 * @param string $charset
	 */
	public function charset($charset): self
	{
		$this->set_charset($charset);
		return $this;
	}

	/**
	 * Table collate
	 *
	 * @param string $collate
	 */
	public function collate($collate): self
	{
		$this->set_collate($collate);
		return $this;
	}

	/**
	 * Indicates if CREATE tABLE should abort if the table already exists
	 *
	 * @param boolean $ifnotexists
	 */
	public function ifnotexists($ifnotexists = true): self
	{
		$this->set_ifnotexists($ifnotexists);
		return $this;
	}
	#endregion

	#region Helpers
	/**
	 * Add an index
	 *
	 * @param string ...$columns
	 */
	public function index(...$columns): Index
	{
		$index = new Index($columns);
		$this->add_index($index);
		return $index;
	}

	/**
	 * Add a unique index
	 *
	 * @param string ...$columns
	 */
	public function unique(...$columns): Index
	{
		$index = new Index($columns);
		$index->unique();
		$this->add_index($index);
		return $index;
	}

	/**
	 * Add a full text index
	 *
	 * @param string ...$columns
	 */
	public function fulltext(...$columns): Index
	{
		$index = new Index($columns);
		$index->fulltext();
		$this->add_index($index);
		return $index;
	}

	/**
	 * Add a spatial index
	 *
	 * @param string ...$columns
	 */
	public function spatial(...$columns): Index
	{
		$index = new Index($columns);
		$index->spatial();
		$this->add_index($index);
		return $index;
	}

	/**
	 * Add a primary key
	 *
	 * @param string ...$columns
	 * @return Primary
	 */
	public function primary(...$columns): Primary
	{
		$index = new Primary($columns);
		$this->add_index($index);
		return $index;
	}

	/**
	 * Add a foreign key
	 *
	 * @param string ...$columns
	 */
	public function foreign_key(...$columns): ForeignKey
	{
		$fk = new ForeignKey($columns);
		$this->add_index($fk);
		return $fk;
	}
	#endregion

	/**
	 * Reset the query
	 */
	public function new_query(): self
	{
		$this->new_query_charset();
		$this->new_query_collate();
		$this->new_query_ifnotexists();
		return $this;
	}

	/**
	 * Get the query SQL
	 */
	public function parse_query(): string
	{
		if (!$this->_name)
			throw new \UnexpectedValueException("A name must be defined");

		$nl = $this->_prettify ? "\n" : '';
		$nlt = $this->_prettify ? "\n\t" : '';
		$columns = $this->_columns ? implode(', '.$nlt, array_map(function(Column $column) { return $column->parse_query(); }, $this->_columns)) : '';
		$indexes = $this->_indexes ? implode(', '.$nlt, array_map(function(Query $index) { return $index->parse_query(); }, $this->_indexes)) : '';
		$sep = $columns && $indexes ? ', '.$nlt : '';

		return "CREATE TABLE{$this->parse_query_ifnotexists()} {$this->_backtick($this->_name)} ($nlt$columns$sep$indexes$nl){$this->parse_query_charset()}{$this->parse_query_collate()} ENGINE = InnoDB";
	}

	/**
	 * Create a new CREATE TABLE statement
	 *
	 * @param string $name Table name
	 * @param bool $ifnotexists Create table only if it does not exist
	 */
	public static function create(string $name = null, bool $ifnotexists = false): self
	{
		return (new static())->name($name)->ifnotexists($ifnotexists);
	}
}