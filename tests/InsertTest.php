<?php

use PHPUnit\Framework\TestCase;
use SQLBuilder\SQL;
use SQLBuilder\Condition;

class InsertTest extends TestCase
{
	public function testInsert()
	{
		$this->assertEquals("INSERT INTO `users` (`string`, `bool`, `int`, `double`) VALUES ('a', b'1', 1, 3.5)",
			SQL::insert()->table('users')->fields('string', 'bool', 'int', 'double')->values(['a', true, 1, 3.5])->parse_query()
		);
	}

	public function testInsertMultiple()
	{
		$this->assertEquals("INSERT INTO `users` (`name`, `type`, `qty`) VALUES ('a', 'b', 1), ('b', 'c', 2)",
			SQL::insert()->table('users')->fields('name', 'type', 'qty')->multiple_values([['a', 'b', 1], ['b', 'c', 2]])->parse_query()
		);
	}

	public function testInsertSelect()
	{
		$this->assertEquals("INSERT INTO `t1` (`a`, `b`, `c`) SELECT `a`, `b`, `c` FROM `t2` WHERE `d` = 1",
			SQL::insert_select()->table('t1')->fields('a', 'b', 'c')->select(
				SQL::select()->select('a')->select('b')->select('c')->from('t2')->where(Condition::eq('d', 1))
			)->parse_query()
		);
	}
}