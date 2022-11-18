<?php
namespace SQLBuilder;

use SQLBuilder\Enums\SQLAction;

/**
 * SQL helper functions.
 *
 * @version 1.0
 * @author 86Dev
 */
abstract class SQL
{
	/**
	 * Surround a name with bactick.
	 * If name contains a table name or alias (eg: 'a.name'), the table and name will be surrounded by backticks.
	 * @param string $name
	 * @return string
	 */
	public static function backtick($field)
	{
		if (empty($field)) return $field;
		return implode('.', array_map(function($part) { return "`$part`"; }, explode('.', $field)));
	}

	/**
	 * Surround a value with quote and add slashes
	 * @param string $value
	 * @return string
	 */
	public static function quote($value)
	{
		if (is_null($value)) return $value;
		return "'".addslashes($value)."'";
	}

	/**
	 * Build a CREATE TABLE query
	 *
	 * @param string $table
	 * @param boolean $ifnotexists Create the table only if it does not exist
	 * @return Statements\CreateTable
	 */
	public static function create_table($table, $ifnotexists = false)
	{
		return Statements\CreateTable::create($table, $ifnotexists);
	}

	/**
	 * Build a ALTER TABLE query
	 *
	 * @param string $table
	 * @return Statements\AlterTable
	 */
	public static function alter_table($table)
	{
		return Statements\AlterTable::create($table);
	}

	/**
	 * Build a DROP TABLE query
	 *
	 * @param string $table Table name
	 * @param boolean $ifexists Delete the table only if it exists
	 * @return Statements\DropTable
	 */
	public static function drop_table($table, $ifexists = false)
	{
		return Statements\DropTable::create($table, $ifexists);
	}

	/**
	 * Build a DROP INDEX query
	 *
	 * @param string $name
	 * @param string $table
	 * @return IndexCommand
	 */
	public static function drop_index($name, $table)
	{
		return IndexCommand::create()->action(SQLAction::DROP)->name($name)->table($table);
	}

	/**
	 * Build a SELECT query
	 *
	 * @return Statements\Select
	 */
	public static function select()
	{
		return Statements\Select::create();
	}

	/**
	 * Build an UPDATE query
	 *
	 * @return Statements\Update
	 */
	public static function update()
	{
		return Statements\Update::create();
	}

	/**
	 * Build an INSERT query
	 *
	 * @return Statements\Insert
	 */
	public static function insert()
	{
		return Statements\Insert::create();
	}

	/**
	 * Build an INSERT ... SELECT query
	 *
	 * @return Statements\InsertSelect
	 */
	public static function insert_select()
	{
		return Statements\InsertSelect::create();
	}

	/**
	 * Build a DELETE query
	 *
	 * @return Statements\Delete
	 */
	public static function delete()
	{
		return Statements\Delete::create();
	}
}