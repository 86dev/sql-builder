<?php

namespace SQLBuilder;

use SQLBuilder\Enums\SQLAction;

/**
 * Column definition builder
 *
 * @version 1.0
 * @author 86Dev
 */
class Column extends Statements\Query
{
	use Statements\Traits\ActionTrait;
	use Statements\Traits\NameTrait;
	use Statements\Traits\ForeignKeyTrait;
	use Statements\Traits\UniqueTrait;
	use Statements\Traits\CharsetTrait;
	use Statements\Traits\CollateTrait;
	use Statements\Traits\CommentTrait;

	const ACTIONS = [SQLAction::ADD, SQLAction::DROP, SQLAction::CHANGE, SQLAction::MODIFY];

	#region Variables
	/**
	 * Column type
	 * @var string
	 */
	protected $_type;

	/**
	 * Column length, used by BIT, *INT, REAL, DOUBLE, FLOAT, DECIMAL, NUMERIC, DATETIME, TIME, TIMESTAMP, CHAR, VARCHAR, BINARY, VARBINARY, BLOB and TEXT
	 *
	 * @var int|null
	 */
	protected $_length;

	/**
	 * Column precision, used by REAL, FLOAT, DOUBLE, DECIMAL and NUMERIC
	 *
	 * @var int|null
	 */
	protected $_precision;

	/**
	 * Indicates if the column is auto incremented
	 *
	 * @var bool
	 */
	protected $_auto_increment;

	/**
	 * Indicates if the column is used has primary key
	 *
	 * @var bool
	 */
	protected $_primary;

	/**
	 * Indicates if the column is unsigned, used by *INT, REAL, DOUBLE, FLOAT, DECIMAL and NUMERIC
	 *
	 * @var bool
	 */
	protected $_unsigned;

	/**
	 * Indicates if the column is left padded with 0 instead of spaces, used by *INT, REAL, DOUBLE, FLOAT, DECIMAL and NUMERIC
	 *
	 * @var bool
	 */
	protected $_zerofill;

	/**
	 * Indicates if the column accepts null value
	 *
	 * @var bool
	 */
	protected $_nullable;

	/**
	 * Column default value
	 *
	 * @var mixed
	 */
	protected $_default;

	/**
	 * Indicates if the default value should be quoted
	 *
	 * @var bool
	 */
	protected $_default_quoted;

	/**
	 * Column default value validator, must be a regex string
	 *
	 * @var string
	 */
	protected $_default_validator;

	/**
	 * Column values, used by ENUM and SET
	 *
	 * @var mixed
	 */
	protected $_values;

	protected $_after;
	protected $_first;
	protected $_new_name;
	#endregion

	/**
	 * Constructor that allows configuring column name and type
	 *
	 * @param string $name Column name
	 * @param string $type Column type
	 */
	public function __construct($name = null, $type = null)
	{
		parent::__construct();
		$this->_name = $name;
		$this->_type = $type;
	}

	/**
	 * Reset the query
	 *
	 * @return Column
	 */
	public function new_query()
	{
		$this->new_query_action();
		$this->new_query_name();
		$this->new_query_unique();
		$this->new_query_foreignkey();
		$this->new_query_charset();
		$this->new_query_collate();

		$this->_type = '';
		$this->_length = null;
		$this->_precision = null;
		$this->_auto_increment = false;
		$this->_primary = false;
		$this->_unsigned = false;
		$this->_zerofill = false;
		$this->_nullable = false;
		$this->_default = null;
		$this->_default_quoted = false;
		$this->_default_validator = null;
		$this->_values = null;
		$this->_after = null;
		$this->_first = false;
		$this->_new_name = null;
	}

	/**
	 * Get the query SQL
	 *
	 * @return string
	 */
	public function parse_query()
	{
		$action = $this->_action;
		if ($action)
			$action .= ' COLUMN';
		if ($this->_action === SQLAction::DROP)
		{
			return "DROP COLUMN {$this->parse_query_name()}";
		}

		$values = '';
		if (is_array($this->_values))
		{
			$values = ' ('.join(', ', array_map([$this, '_quote'], $this->_values)).')';
		}

		$length = '';
		if (in_array($this->_type, ['VARCHAR', 'VARBINARY']) && $this->_length === null)
			$this->_length = 255;
		if ($this->_length !== null)
		{
			if ($this->_precision !== null)
				$length = "($this->_length, $this->_precision)";
			else
				$length = "($this->_length)";
		}

		$default = '';
		if ($this->_default !== null)
		{
			if ($this->_default_validator && !preg_match($this->_default_validator, $this->_default))
				throw new \UnexpectedValueException("Column $this->_name: default value \"$this->_default\" is not valid.");

			$default = $this->_default;
			if ($this->_type === 'BIT')
			{
				if (!$default) $default = 0;
				$default = 'b'.$this->_quote($default);
			}
			else if ($this->_default_quoted)
				$default = $this->_quote($default);
			$default = " DEFAULT $default";
		}

		$after = $this->_after ? ' AFTER '.$this->_backtick($this->_after) : '';
		$first = $this->_first ? ' FIRST' : '';
		$new_name = '';
		if ($this->_action === SQLAction::CHANGE) // new name is mandatory for CHANGE statement
			$new_name = ' '.($this->_new_name ? $this->_backtick($this->_new_name) : $this->parse_query_name());

		return trim("$action {$this->parse_query_name()}$new_name $this->_type"
			.$values
			.$length
			.($this->_unsigned ? ' UNSIGNED' : '')
			.($this->_zerofill ? ' ZEROFILL' : '')
			.($this->_charset !== null ? " CHARACTER SET $this->_charset" : '')
			.($this->_collate !== null ? " COLLATE $this->_collate" : '')
			.($this->_nullable ? ' NULL' : ' NOT NULL')
			.$default
			.($this->_auto_increment ? ' AUTO_INCREMENT' : '')
			.$this->parse_query_unique()
			.($this->_primary ? ' PRIMARY KEY' : '')
			.$this->parse_query_foreignkey()
			.$after
			.$first);
	}

	/**
	 * Get default name
	 *
	 * @return void
	 */
	protected function _default_name()
	{
		return $this->_name;
	}

	#region Setters
	/**
	 * Column name
	 *
	 * @param string $name
	 * @return Column
	 */
	public function name($name)
	{
		$this->set_name($name);
		return $this;
	}

	/**
	 * Column comment
	 *
	 * @param string $comment
	 * @return Column
	 */
	public function comment($comment)
	{
		$this->set_comment($comment);
		return $this;
	}

	/**
	 * Column new name
	 *
	 * @param string $new_name
	 * @return Column
	 */
	public function new_name($new_name)
	{
		$this->_new_name = $new_name;
		return $this;
	}

	/**
	 * Column type
	 *
	 * @param string $type
	 * @return Column
	 */
	public function type($type)
	{
		$this->_type = $type;
		return $this;
	}

	/**
	 * Column action, see Column::ACTIONS for available values
	 *
	 * @var string
	 * @return Column
	 */
	public function action($action)
	{
		$this->set_action($action);
		return $this;
	}

	/**
	 * Column length, used by BIT, *INT, REAL, DOUBLE, FLOAT, DECIMAL, NUMERIC, DATETIME, TIME, TIMESTAMP, CHAR, VARCHAR, BINARY, VARBINARY, BLOB and TEXT
	 *
	 * @param int|null $length
	 * @return Column
	 */
	public function length($length)
	{
		$this->_length = $length;
		return $this;
	}

	/**
	 * Column precision, used by REAL, FLOAT, DOUBLE, DECIMAL and NUMERIC
	 *
	 * @param int|null $precision
	 * @return Column
	 */
	public function precision($precision)
	{
		$this->_precision = $precision;
		return $this;
	}

	/**
	 * Indicates if the column is auto incremented
	 *
	 * @param bool $auto_increment
	 * @return Column
	 */
	public function auto_increment($auto_increment = true)
	{
		$this->_auto_increment = $auto_increment;
		return $this;
	}

	/**
	 * Indicates if the column has a unique index
	 *
	 * @param bool $unique
	 * @return Column
	 */
	public function unique($unique = true)
	{
		$this->set_unique($unique);
		return $this;
	}

	/**
	 * Indicates if the column is used has primary key
	 *
	 * @param bool $primary
	 * @return Column
	 */
	public function primary($primary = true)
	{
		$this->_primary = $primary;
		return $this;
	}

	/**
	 * Indicates if the column is unsigned, used by *INT, REAL, DOUBLE, FLOAT, DECIMAL and NUMERIC
	 *
	 * @param bool $unsigned
	 * @return Column
	 */
	public function unsigned($unsigned = true)
	{
		$this->_unsigned = $unsigned;
		return $this;
	}

	/**
	 * Indicates if the column is left padded with 0 instead of spaces, used by *INT, REAL, DOUBLE, FLOAT, DECIMAL and NUMERIC
	 *
	 * @param bool $zerofill
	 * @return Column
	 */
	public function zerofill($zerofill = true)
	{
		$this->_zerofill = $zerofill;
		return $this;
	}

	/**
	 * Indicates if the column accepts null value
	 *
	 * @param bool $nullable
	 * @return Column
	 */
	public function nullable($nullable = true)
	{
		$this->_nullable = $nullable;
		return $this;
	}

	/**
	 * Column default value
	 *
	 * @param string $default
	 * @return Column
	 */
	public function _default($default)
	{
		$this->_default = $default;
		return $this;
	}

	/**
	 * Column default value set to CURRENT_TIMESTAMP. Will only work for DATETIME and TIMESTAMP columns.
	 *
	 * @param bool $also_on_update Determine if an UPDATE on the row will also update the field with CURRENT_TIMESTAMP
	 * @return Column
	 */
	public function _default_timestamp($also_on_update = false)
	{
		$this->_default = 'CURRENT_TIMESTAMP';
		if ($also_on_update)
			$this->_default .= ' ON UPDATE CURRENT_TIMESTAMP';
		$this->default_quoted(false);
		$this->default_validator(null);
		return $this;
	}

	/**
	 * Indicates if the default value should be quoted
	 *
	 * @param bool $default_quoted
	 * @return Column
	 */
	public function default_quoted($default_quoted = true)
	{
		$this->_default_quoted = $default_quoted;
		return $this;
	}

	/**
	 * Column default value validator, must be a regex string
	 *
	 * @param string $default_validator
	 * @return Column
	 */
	public function default_validator($default_validator)
	{
		$this->_default_validator = $default_validator;
		return $this;
	}

	/**
	 * Column charset, used by CHAR, VARCHAR, *TEXT, ENUM and SET
	 *
	 * @param string $charset
	 * @return Column
	 */
	public function charset($charset)
	{
		$this->set_charset($charset);
		return $this;
	}

	/**
	 * Column collation, used by CHAR, VARCHAR, *TEXT, ENUM and SET
	 *
	 * @param string $collate
	 * @return Column
	 */
	public function collate($collate)
	{
		$this->set_collate($collate);
		return $this;
	}

	/**
	 * Column values, used by ENUM and SET
	 *
	 * @param string[] $values
	 * @return Column
	 */
	public function values($values)
	{
		$this->_values = $values;
		return $this;
	}

	/**
	 * Column references table
	 *
	 * @param string $on
	 * @return Column
	 */
	public function on($on)
	{
		$this->set_on($on);
		return $this;
	}

	/**
	 * Column references columns names
	 *
	 * @param string|string[] $references
	 * @return Column
	 */
	public function references(...$references)
	{
		$this->set_references($references);
		return $this;
	}

	/**
	 * Column references delete action
	 *
	 * @param string $delete See SQL_Index::REF_* for available values
	 * @throws \UnexpectedValueException If value is not in SQL_Index::REF_OPTIONS
	 * @return Column
	 */
	public function delete($delete)
	{
		$this->set_delete($delete);
		return $this;
	}

	/**
	 * Column references update action
	 *
	 * @param string $update See SQL_Index::REF_* for available values
	 * @throws \UnexpectedValueException If value is not in SQL_Index::REF_OPTIONS
	 * @return Column
	 */
	public function update($update)
	{
		$this->set_update($update);
		return $this;
	}

	/**
	 * Column references match
	 *
	 * @param string $match See ForeignKeyMatch for available values
	 * @throws \UnexpectedValueException If value is not in ForeignKeyMatch
	 * @return Column
	 */
	public function match($match)
	{
		$this->set_match($match);
		return $this;
	}

	/**
	 * Define after which field this field should be set
	 *
	 * @param string $field_name
	 * @return Column
	 */
	public function after($field_name)
	{
		$this->_first = false;
		$this->_after = $field_name;
		return $this;
	}

	/**
	 * Define if this field should be set first
	 *
	 * @param string $field_name
	 * @return Column
	 */
	public function first($value = true)
	{
		$this->_after = null;
		$this->_first = $value;
		return $this;
	}
	#endregion

	#region Type Helpers
	/**
	 * Create a new Column query
	 *
	 * @return Column
	 */
	public static function create()
	{
		return new static();
	}

	/**
	 * Configure an INT column (-2,147,483,648 to 2,147,483,647; unsigned 0 to 4,294,967,295)
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function int($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a TINYINT column (-128 to 127; unsigned 0 to 255)
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function tinyint($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a SMALLINT column (-32,768 to 32,767; unsigned 0 to 65,535)
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function smallint($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a MEDIUMINT column (-8,388,608 to 8,388,607, unsigned 0 to 16,777,215)
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function mediumint($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a BIGINT column (-2^63 to 2^63-1, unsigned 0 to 2^64-1)
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function bigint($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a BIT column (1 to 64)
	 * The BIT data type is used to store bit values. A type of BIT(M) enables storage of M-bit values. M can range from 1 to 64.
	 * To specify bit values, b'value' notation can be used. value is a binary value written using zeros and ones. For example, b'111' and b'10000000' represent 7 and 128, respectively.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function bit($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a DOUBLE column (precision 0 to 53)
	 * The FLOAT and DOUBLE types represent approximate numeric data values. MySQL uses four bytes for single-precision values and eight bytes for double-precision values.
	 * For FLOAT, the SQL standard permits an optional specification of the precision (but not the range of the exponent) in bits following the keyword FLOAT in parentheses. MySQL also supports this optional precision specification, but the precision value is used only to determine storage size. A precision from 0 to 23 results in a 4-byte single-precision FLOAT column. A precision from 24 to 53 results in an 8-byte double-precision DOUBLE column.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function double($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a FLOAT column (precision 0 to 23)
	 * The FLOAT and DOUBLE types represent approximate numeric data values. MySQL uses four bytes for single-precision values and eight bytes for double-precision values.
	 * For FLOAT, the SQL standard permits an optional specification of the precision (but not the range of the exponent) in bits following the keyword FLOAT in parentheses. MySQL also supports this optional precision specification, but the precision value is used only to determine storage size. A precision from 0 to 23 results in a 4-byte single-precision FLOAT column. A precision from 24 to 53 results in an 8-byte double-precision DOUBLE column.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function float($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a DECIMAL column
	 * The DECIMAL and NUMERIC types store exact numeric data values. These types are used when it is important to preserve exact precision, for example with monetary data. In MySQL, NUMERIC is implemented as DECIMAL, so the following remarks about DECIMAL apply equally to NUMERIC.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function decimal($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a DATE column
	 * The DATE type is used for values with a date part but no time part. MySQL retrieves and displays DATE values in 'YYYY-MM-DD' format. The supported range is '1000-01-01' to '9999-12-31'.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function date($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a DATETIME column
	 * The DATETIME type is used for values that contain both date and time parts. MySQL retrieves and displays DATETIME values in 'YYYY-MM-DD HH:MM:SS' format. The supported range is '1000-01-01 00:00:00' to '9999-12-31 23:59:59'.
	 * A DATETIME or TIMESTAMP value can include a trailing fractional seconds part in up to microseconds (6 digits) precision.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function datetime($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a TIME column
	 * MySQL retrieves and displays TIME values in 'HH:MM:SS' format (or 'HHH:MM:SS' format for large hours values). TIME values may range from '-838:59:59' to '838:59:59'. The hours part may be so large because the TIME type can be used not only to represent a time of day (which must be less than 24 hours), but also elapsed time or a time interval between two events (which may be much greater than 24 hours, or even negative).
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function time($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a TIMESTAMP column
	 * The TIMESTAMP data type is used for values that contain both date and time parts. TIMESTAMP has a range of '1970-01-01 00:00:01' UTC to '2038-01-19 03:14:07' UTC.
	 * A DATETIME or TIMESTAMP value can include a trailing fractional seconds part in up to microseconds (6 digits) precision.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function timestamp($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a YEAR column
	 * The YEAR type is a 1-byte type used to represent year values. It can be declared as YEAR or YEAR(4) and has a display width of four characters.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function year($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a CHAR column (0 - 255)
	 * The length of a CHAR column is fixed to the length that you declare when you create the table. The length can be any value from 0 to 255. When CHAR values are stored, they are right-padded with spaces to the specified length. When CHAR values are retrieved, trailing spaces are removed unless the PAD_CHAR_TO_FULL_LENGTH SQL mode is enabled.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function char($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a VARCHAR column (0 - 65,535)
	 * Values in VARCHAR columns are variable-length strings. The length can be specified as a value from 0 to 65,535. The effective maximum length of a VARCHAR is subject to the maximum row size (65,535 bytes, which is shared among all columns) and the character set used.
	 * In contrast to CHAR, VARCHAR values are stored as a 1-byte or 2-byte length prefix plus data. The length prefix indicates the number of bytes in the value. A column uses one length byte if values require no more than 255 bytes, two length bytes if values may require more than 255 bytes.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function varchar($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a BINARY column (0 - 255 bytes)
	 * The BINARY and VARBINARY types are similar to CHAR and VARCHAR, except that they contain binary strings rather than nonbinary strings. That is, they contain byte strings rather than character strings. This means they have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in the values.
	 * The permissible maximum length is the same for BINARY and VARBINARY as it is for CHAR and VARCHAR, except that the length for BINARY and VARBINARY is a length in bytes rather than in characters.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function binary($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a VARBINARY column (0 - 255 bytes)
	 * The BINARY and VARBINARY types are similar to CHAR and VARCHAR, except that they contain binary strings rather than nonbinary strings. That is, they contain byte strings rather than character strings. This means they have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in the values.
	 * The permissible maximum length is the same for BINARY and VARBINARY as it is for CHAR and VARCHAR, except that the length for BINARY and VARBINARY is a length in bytes rather than in characters.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function varbinary($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a TINYBLOB column
	 * A BLOB is a binary large object that can hold a variable amount of data. BLOB values are treated as binary strings (byte strings). They have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in column values.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function tinyblob($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a BLOB column
	 * A BLOB is a binary large object that can hold a variable amount of data. BLOB values are treated as binary strings (byte strings). They have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in column values.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function blob($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a MEDIUMBLOB column
	 * A BLOB is a binary large object that can hold a variable amount of data. BLOB values are treated as binary strings (byte strings). They have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in column values.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function mediumblob($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a LONGBLOB column
	 * A BLOB is a binary large object that can hold a variable amount of data. BLOB values are treated as binary strings (byte strings). They have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in column values.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function longblob($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a TINYTEXT column (0 - 255)
	 * A TEXT is a binary large object that can hold a variable amount of data. TEXT values are treated as nonbinary strings (character strings). They have a character set other than binary, and values are sorted and compared based on the collation of the character set.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function tinytext($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a TEXT column (0 - 65k)
	 * A TEXT is a binary large object that can hold a variable amount of data. TEXT values are treated as nonbinary strings (character strings). They have a character set other than binary, and values are sorted and compared based on the collation of the character set.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function text($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a MEDIUMTEXT column (0 - 16.7M)
	 * A TEXT is a binary large object that can hold a variable amount of data. TEXT values are treated as nonbinary strings (character strings). They have a character set other than binary, and values are sorted and compared based on the collation of the character set.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function mediumtext($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a LONGTEXT column (0 - 4.3G)
	 * A TEXT is a binary large object that can hold a variable amount of data. TEXT values are treated as nonbinary strings (character strings). They have a character set other than binary, and values are sorted and compared based on the collation of the character set.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function longtext($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure an ENUM column
	 * An ENUM is a string object with a value chosen from a list of permitted values
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function enum($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a SET column
	 * A SET is a string object that can have zero or more values, each of which must be chosen from a list of permitted values specified when the table is created. SET column values that consist of multiple set members are specified with members separated by commas (,). A consequence of this is that SET member values should not themselves contain commas.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function set($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}

	/**
	 * Configure a JSON column
	 * As of MySQL 5.7.8, MySQL supports a native JSON data type defined by RFC 7159 that enables efficient access to data in JSON (JavaScript Object Notation) documents. The JSON data type provides these advantages over storing JSON-format strings in a string column:
	 * - Automatic validation of JSON documents stored in JSON columns. Invalid documents produce an error.
	 * - Optimized storage format. JSON documents stored in JSON columns are converted to an internal format that permits quick read access to document elements. When the server later must read a JSON value stored in this binary format, the value need not be parsed from a text representation. The binary format is structured to enable the server to look up subobjects or nested values directly by key or array index without reading all values before or after them in the document.
	 *
	 * @param string $name Column name
	 * @return Column
	 */
	public static function json($name)
	{
		return new static($name, strtoupper(__FUNCTION__));
	}
	#endregion
}