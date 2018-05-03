<?php

use PHPUnit\Framework\TestCase;
use SQLBuilder\SQL;
use SQLBuilder\Condition;
use SQLBuilder\Join;

class DeleteTest extends TestCase
{
	public function testDelete()
	{
		$this->assertEquals("DELETE FROM `users` WHERE `id` = 1",
			SQL::delete()->from('users')->where(Condition::eq('id', 1))->parse_query()
		);
	}
	
	public function testDeleteUsing()
	{
		$this->assertEquals("DELETE FROM `u` USING `users` AS `u` INNER JOIN `users_roles` AS `ur` ON `ur`.`user_id` = `u`.`id` WHERE `ur`.`role_id` = 1",
			SQL::delete()->from('u')->using('users', 'u')->join(Join::inner_join('users_roles', 'ur')->on(Condition::column('ur.user_id', 'u.id')))->where(Condition::eq('ur.role_id', 1))->parse_query()
		);
	}
}