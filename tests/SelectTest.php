<?php

use SQLBuilder\SQL;
use SQLBuilder\Condition;
use PHPUnit\Framework\TestCase;
use SQLBuilder\Join;

class SelectTest extends TestCase
{
	public function testSelectFrom()
	{
		$this->assertEquals('SELECT `name` FROM `users`',
			SQL::select()->select('name')->from('users')->parse_query());
	}

	public function testSelectFromWhere()
	{
		$this->assertEquals('SELECT `name` FROM `users` WHERE `id` = 1',
			SQL::select()->select('name')->from('users')->where(['id' => 1])->parse_query());
	}

	public function testSelectFromJoinWhere()
	{
		$this->assertEquals('SELECT `u`.`name` FROM `users` AS `u` INNER JOIN `users_roles` AS `ur` ON `ur`.`user_id` = `u`.`id` WHERE `ur`.`role_id` = 1',
			SQL::select()
				->select('name', 'u')
				->from('users', 'u')
				->join(Join::inner_join('users_roles', 'ur')->on(Condition::column('ur.user_id', 'u.id')))
				->where(['ur.role_id' => 1])
				->parse_query());
	}

	public function testSelectFromWhereGroupby()
	{
		$this->assertEquals('SELECT `name` FROM `users` WHERE `id` = 1 GROUP BY `name`',
			SQL::select()
				->select('name', '', '', true)
				->from('users')
				->where(['id' => 1])
				->parse_query());

		$this->assertEquals('SELECT `name` FROM `users` WHERE `id` = 1 GROUP BY `name`',
		SQL::select()
			->select('name')
			->from('users')
			->where(['id' => 1])
			->groupby('name')
			->parse_query());
	}

	public function testSelectFromWhereGroupbyHaving()
	{
		$this->assertEquals('SELECT `name` FROM `users` WHERE `id` = 1 GROUP BY `name` HAVING COUNT(*) > 1',
		SQL::select()
			->select('name')
			->from('users')
			->where(['id' => 1])
			->groupby('name')
			->having(Condition::gt('COUNT(*)', 1, null, true))
			->parse_query());
	}

	public function testSelectFromOrderby()
	{
		$this->assertEquals('SELECT `name` FROM `users` ORDER BY `name` ASC',
			SQL::select()
				->select('name')
				->from('users')
				->orderby('name')
				->parse_query());

		$this->assertEquals('SELECT `name` FROM `users` ORDER BY `name` DESC',
		SQL::select()
			->select('name')
			->from('users')
			->orderby('name', 'DESC')
			->parse_query());
	}

	public function testSelectFromLimit()
	{
		$this->assertEquals('SELECT `name` FROM `users` LIMIT 10',
			SQL::select()->select('name')->from('users')->limit(10)->parse_query());
	}

	public function testSelectFromOffset()
	{
		$this->assertEquals('SELECT `name` FROM `users` OFFSET 10',
			SQL::select()->select('name')->from('users')->offset(10)->parse_query());
	}

	public function testSelectFromLimitOffset()
	{
		$this->assertEquals('SELECT `name` FROM `users` LIMIT 10 OFFSET 10',
			SQL::select()->select('name')->from('users')->offset(10)->limit(10)->parse_query());
	}
}