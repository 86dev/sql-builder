<?php

use SQLBuilder\SQL;
use SQLBuilder\Condition;
use PHPUnit\Framework\TestCase;
use SQLBuilder\Join;
use SQLBuilder\Enums\ConditionType;

class UpdateTest extends TestCase
{
	public function testUpdate()
	{
		$this->assertEquals("UPDATE `users` SET `name` = 'a' WHERE `id` = 1",
			SQL::update()->table('users')->set('name', 'a')->where(Condition::eq('id', 1))->parse_query()
		);
	}

	public function testUpdateSetMany()
	{
		$this->assertEquals("UPDATE `users` SET `string` = 'a', `int` = 1, `float` = 3.5, `bool` = b'1', `null` = NULL WHERE `id` = 1",
			SQL::update()->table('users')->set_many(['string' => 'a', 'int' => 1, 'float' => 3.5, 'bool' => true, 'null' => null])->where(Condition::eq('id', 1))->parse_query()
		);
	}

	public function testUpdateJoin()
	{
		$this->assertEquals("UPDATE `users` AS `u` INNER JOIN `tests` AS `t` ON `t`.`user_id` = `u`.`id` SET `u`.`name` = `t`.`name` WHERE `u`.`id` = 1",
			SQL::update()
				->table('users')
				->alias('u')
				->join(Join::inner_join('tests', 't')->on(Condition::column('user_id', 'u.id', 't')))
				->set('name', 't.name', 'u', ConditionType::COLUMN)
				->where(Condition::eq('id', 1, 'u'))
				->parse_query()
		);
	}
}