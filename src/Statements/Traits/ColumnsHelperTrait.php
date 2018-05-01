<?php

namespace SQLBuilder\Statements\Traits;
use SQLBuilder\Column;

/**
 * Define column helpers function
 *
 * @version 1.0
 * @author 86Dev
 */
trait ColumnsHelperTrait
{
	/**
	 * Configure an INT column (-2,147,483,648 to 2,147,483,647; unsigned 0 to 4,294,967,295)
	 * @param \string $name Column name
	 * @return Column
	 */
	public function int($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a TINYINT column (-128 to 127; unsigned 0 to 255)
	 * @param \string $name Column name
	 * @return Column
	 */
	public function tinyint($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a SMALLINT column (-32,768 to 32,767; unsigned 0 to 65,535)
	 * @param \string $name Column name
	 * @return Column
	 */
	public function smallint($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a MEDIUMINT column (-8,388,608 to 8,388,607, unsigned 0 to 16,777,215)
	 * @param \string $name Column name
	 * @return Column
	 */
	public function mediumint($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a BIGINT column (-2^63 to 2^63-1, unsigned 0 to 2^64-1)
	 * @param \string $name Column name
	 * @return Column
	 */
	public function bigint($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a BIT column (1 to 64)
	 * The BIT data type is used to store bit values. A type of BIT(M) enables storage of M-bit values. M can range from 1 to 64.
	 * To specify bit values, b'value' notation can be used. value is a binary value written using zeros and ones. For example, b'111' and b'10000000' represent 7 and 128, respectively.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function bit($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a DOUBLE column (precision 0 to 53)
	 * The FLOAT and DOUBLE types represent approximate numeric data values. MySQL uses four bytes for single-precision values and eight bytes for double-precision values.
	 * For FLOAT, the SQL standard permits an optional specification of the precision (but not the range of the exponent) in bits following the keyword FLOAT in parentheses. MySQL also supports this optional precision specification, but the precision value is used only to determine storage size. A precision from 0 to 23 results in a 4-byte single-precision FLOAT column. A precision from 24 to 53 results in an 8-byte double-precision DOUBLE column.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function double($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a FLOAT column (precision 0 to 23)
	 * The FLOAT and DOUBLE types represent approximate numeric data values. MySQL uses four bytes for single-precision values and eight bytes for double-precision values.
	 * For FLOAT, the SQL standard permits an optional specification of the precision (but not the range of the exponent) in bits following the keyword FLOAT in parentheses. MySQL also supports this optional precision specification, but the precision value is used only to determine storage size. A precision from 0 to 23 results in a 4-byte single-precision FLOAT column. A precision from 24 to 53 results in an 8-byte double-precision DOUBLE column.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function float($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a DECIMAL column
	 * The DECIMAL and NUMERIC types store exact numeric data values. These types are used when it is important to preserve exact precision, for example with monetary data. In MySQL, NUMERIC is implemented as DECIMAL, so the following remarks about DECIMAL apply equally to NUMERIC.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function decimal($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a DATE column
	 * The DATE type is used for values with a date part but no time part. MySQL retrieves and displays DATE values in 'YYYY-MM-DD' format. The supported range is '1000-01-01' to '9999-12-31'.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function date($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a DATETIME column
	 * The DATETIME type is used for values that contain both date and time parts. MySQL retrieves and displays DATETIME values in 'YYYY-MM-DD HH:MM:SS' format. The supported range is '1000-01-01 00:00:00' to '9999-12-31 23:59:59'.
	 * A DATETIME or TIMESTAMP value can include a trailing fractional seconds part in up to microseconds (6 digits) precision.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function datetime($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a TIME column
	 * MySQL retrieves and displays TIME values in 'HH:MM:SS' format (or 'HHH:MM:SS' format for large hours values). TIME values may range from '-838:59:59' to '838:59:59'. The hours part may be so large because the TIME type can be used not only to represent a time of day (which must be less than 24 hours), but also elapsed time or a time interval between two events (which may be much greater than 24 hours, or even negative).
	 * @param \string $name Column name
	 * @return Column
	 */
	public function time($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a TIMESTAMP column
	 * The TIMESTAMP data type is used for values that contain both date and time parts. TIMESTAMP has a range of '1970-01-01 00:00:01' UTC to '2038-01-19 03:14:07' UTC.
	 * A DATETIME or TIMESTAMP value can include a trailing fractional seconds part in up to microseconds (6 digits) precision.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function timestamp($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a YEAR column
	 * The YEAR type is a 1-byte type used to represent year values. It can be declared as YEAR or YEAR(4) and has a display width of four characters.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function year($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a CHAR column (0 - 255)
	 * The length of a CHAR column is fixed to the length that you declare when you create the table. The length can be any value from 0 to 255. When CHAR values are stored, they are right-padded with spaces to the specified length. When CHAR values are retrieved, trailing spaces are removed unless the PAD_CHAR_TO_FULL_LENGTH SQL mode is enabled.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function char($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a VARCHAR column (0 - 65,535)
	 * Values in VARCHAR columns are variable-length \strings. The length can be specified as a value from 0 to 65,535. The effective maximum length of a VARCHAR is subject to the maximum row size (65,535 bytes, which is shared among all columns) and the character set used.
	 * In contrast to CHAR, VARCHAR values are stored as a 1-byte or 2-byte length prefix plus data. The length prefix indicates the number of bytes in the value. A column uses one length byte if values require no more than 255 bytes, two length bytes if values may require more than 255 bytes.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function varchar($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a BINARY column (0 - 255 bytes)
	 * The BINARY and VARBINARY types are similar to CHAR and VARCHAR, except that they contain binary \strings rather than nonbinary \strings. That is, they contain byte \strings rather than character \strings. This means they have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in the values.
	 * The permissible maximum length is the same for BINARY and VARBINARY as it is for CHAR and VARCHAR, except that the length for BINARY and VARBINARY is a length in bytes rather than in characters.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function binary($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a VARBINARY column (0 - 255 bytes)
	 * The BINARY and VARBINARY types are similar to CHAR and VARCHAR, except that they contain binary \strings rather than nonbinary \strings. That is, they contain byte \strings rather than character \strings. This means they have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in the values.
	 * The permissible maximum length is the same for BINARY and VARBINARY as it is for CHAR and VARCHAR, except that the length for BINARY and VARBINARY is a length in bytes rather than in characters.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function varbinary($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a TINYBLOB column
	 * A BLOB is a binary large object that can hold a variable amount of data. BLOB values are treated as binary \strings (byte \strings). They have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in column values.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function tinyblob($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a BLOB column
	 * A BLOB is a binary large object that can hold a variable amount of data. BLOB values are treated as binary \strings (byte \strings). They have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in column values.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function blob($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a MEDIUMBLOB column
	 * A BLOB is a binary large object that can hold a variable amount of data. BLOB values are treated as binary \strings (byte \strings). They have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in column values.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function mediumblob($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a LONGBLOB column
	 * A BLOB is a binary large object that can hold a variable amount of data. BLOB values are treated as binary \strings (byte \strings). They have the binary character set and collation, and comparison and sorting are based on the numeric values of the bytes in column values.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function longblob($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a TINYTEXT column (0 - 255)
	 * A TEXT is a binary large object that can hold a variable amount of data. TEXT values are treated as nonbinary \strings (character \strings). They have a character set other than binary, and values are sorted and compared based on the collation of the character set.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function tinytext($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a TEXT column (0 - 65k)
	 * A TEXT is a binary large object that can hold a variable amount of data. TEXT values are treated as nonbinary \strings (character \strings). They have a character set other than binary, and values are sorted and compared based on the collation of the character set.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function text($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a MEDIUMTEXT column (0 - 16.7M)
	 * A TEXT is a binary large object that can hold a variable amount of data. TEXT values are treated as nonbinary \strings (character \strings). They have a character set other than binary, and values are sorted and compared based on the collation of the character set.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function mediumtext($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a LONGTEXT column (0 - 4.3G)
	 * A TEXT is a binary large object that can hold a variable amount of data. TEXT values are treated as nonbinary \strings (character \strings). They have a character set other than binary, and values are sorted and compared based on the collation of the character set.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function longtext($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure an ENUM column
	 * An ENUM is a \string object with a value chosen from a list of permitted values
	 * @param \string $name Column name
	 * @return Column
	 */
	public function enum($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a SET column
	 * A SET is a \string object that can have zero or more values, each of which must be chosen from a list of permitted values specified when the table is created. SET column values that consist of multiple set members are specified with members separated by commas (,). A consequence of this is that SET member values should not themselves contain commas.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function set($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}

	/**
	 * Configure a JSON column
	 * As of MySQL 5.7.8, MySQL supports a native JSON data type defined by RFC 7159 that enables efficient access to data in JSON (JavaScript Object Notation) documents. The JSON data type provides these advantages over storing JSON-format \strings in a \string column:
	 * - Automatic validation of JSON documents stored in JSON columns. Invalid documents produce an error.
	 * - Optimized storage format. JSON documents stored in JSON columns are converted to an internal format that permits quick read access to document elements. When the server later must read a JSON value stored in this binary format, the value need not be parsed from a text representation. The binary format is structured to enable the server to look up subobjects or nested values directly by key or array index without reading all values before or after them in the document.
	 * @param \string $name Column name
	 * @return Column
	 */
	public function json($name)
	{
		$column = new Column($name, strtoupper(__FUNCTION__));
		$this->add_column($column);
		return $column;
	}
}
