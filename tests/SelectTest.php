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

	public function testSelectDistinctFromWhere()
	{
		$this->assertEquals('SELECT DISTINCT `name` FROM `users` WHERE `id` = 1',
			SQL::select()->distinct()->select('name')->from('users')->where(['id' => 1])->parse_query());
	}

	public function testSelectFromWhereComplex()
	{
		$this->assertEquals('SELECT `name` FROM `users` WHERE (`id` = 1 OR (`login` LIKE \'%test%\' OR `login` LIKE \'%truc%\'))',
			SQL::select()
				->select('name')
				->from('users')
				->where([
					'relation' => 'OR',
					['field' => 'id', 'values' => 1],
					['field' => 'login', 'values' => ['test', 'truc'], 'operator' => 'LIKE']
				])->parse_query()
		);
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

	public function testSelectMultipleFromJoinWhere()
	{
		$this->assertEquals('SELECT `u`.`name`'
			.' FROM `users` AS `u` INNER JOIN `users_roles` AS `ur` ON `ur`.`user_id` = `u`.`id`,'
			.' `roles` AS `r` INNER JOIN `roles_capabilities` AS `rc` ON `rc`.`role_id` = `r`.`id`'
			.' WHERE (`ur`.`role_id` = 1 AND `rc`.`role_id` = 1)',
			SQL::select()
				->select('name', 'u')
				->from('users', 'u')
				->join(Join::inner_join('users_roles', 'ur')->on(Condition::column('ur.user_id', 'u.id')))
				->from('roles', 'r')
				->join(Join::inner_join('roles_capabilities', 'rc')->on(Condition::column('rc.role_id', 'r.id')))
				->where(['ur.role_id' => 1])
				->where(['rc.role_id' => 1])
				->parse_query());
	}

	public function testSelectAddJoinLater()
	{
		$sql = SQL::select()
			->select('name', 'u')
			->from('users', 'u')
			->from('roles', 'r');

		$sql->join(Join::inner_join('users_roles', 'ur')->on(Condition::column('ur.user_id', 'u.id')), 'u')
			->where(['ur.role_id' => 1]);
		$sql->join(Join::inner_join('roles_capabilities', 'rc')->on(Condition::column('rc.role_id', 'r.id')))
			->where(['rc.role_id' => 1]);

		$this->assertEquals(
			'SELECT `u`.`name` FROM `users` AS `u` INNER JOIN `users_roles` AS `ur` ON `ur`.`user_id` = `u`.`id`,'
			.' `roles` AS `r` INNER JOIN `roles_capabilities` AS `rc` ON `rc`.`role_id` = `r`.`id`'
			.' WHERE (`ur`.`role_id` = 1 AND `rc`.`role_id` = 1)',
			$sql->parse_query());
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
			->having(Condition::gt('COUNT(*)', 1)->do_not_use_backtick())
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