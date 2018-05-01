<?php

use SQLBuilder\SQL;
use SQLBuilder\Condition;
use PHPUnit\Framework\TestCase;

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
		$this->assertEquals("UPDATE `users` SET `name` = 'a', `type` = 0 WHERE `id` = 1",
			SQL::update()->table('users')->set_many(['name' => 'a', 'type' => 0])->where(Condition::eq('id', 1))->parse_query()
		);
	}
}