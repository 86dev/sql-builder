<?php

use SQLBuilder\Condition;
use PHPUnit\Framework\TestCase;
use SQLBuilder\Enums\ConditionType;

class ConditionTest extends TestCase
{
	public function testStringEq()
	{
		$this->assertEquals("`name` = 'test'", Condition::eq('name', 'test'));
	}

	public function testStringEqEmptyNull()
	{
		$this->assertEquals("(`name` IS NULL OR `name` = '')", Condition::eq('name', [null, '']));
	}

	public function testStringNeq()
	{
		$this->assertEquals("`name` != 'test'", Condition::neq('name', 'test'));
	}

	public function testStringNeqEmptyNull()
	{
		$this->assertEquals("(`name` IS NOT NULL AND `name` != '')", Condition::neq('name', [null, '']));
	}

	public function testStringLike()
	{
		$this->assertEquals("`name` LIKE '%test%'", Condition::like('name', 'test'));
	}

	public function testStringLikeMulti()
	{
		$this->assertEquals("(`name` LIKE '%test%' OR `name` LIKE '%truc%')", Condition::like('name', ['test', 'truc']));
	}

	public function testStringNotLike()
	{
		$this->assertEquals("`name` NOT LIKE '%test%'", Condition::not_like('name', 'test'));
	}

	public function testStringIn()
	{
		$this->assertEquals("`name` IN ('a', 'b', 'c')", Condition::in('name', ['a', 'b', 'c']));
	}

	public function testStringNotIn()
	{
		$this->assertEquals("`name` NOT IN ('a', 'b', 'c')", Condition::not_in('name', ['a', 'b', 'c']));
	}

	public function testIsNull()
	{
		$this->assertEquals("`name` IS NULL", Condition::is_null('name'));
	}

	public function testIsNotNull()
	{
		$this->assertEquals("`name` IS NOT NULL", Condition::is_not_null('name'));
	}

	public function testIntEq()
	{
		$this->assertEquals("`name` = 0", Condition::eq('name', 0));
	}

	public function testIntNeq()
	{
		$this->assertEquals("`name` != 0", Condition::neq('name', 0));
	}

	public function testIntNeq0Null()
	{
		$this->assertEquals("(`name` IS NOT NULL AND `name` != 0)", Condition::neq('name', [null, 0]));
	}

	public function testIntLt()
	{
		$this->assertEquals("`name` < 0", Condition::lt('name', 0));
	}

	public function testIntLte()
	{
		$this->assertEquals("`name` <= 0", Condition::lte('name', 0));
	}

	public function testIntGt()
	{
		$this->assertEquals("`name` > 0", Condition::gt('name', 0));
	}

	public function testIntGte()
	{
		$this->assertEquals("`name` >= 0", Condition::gte('name', 0));
	}

	public function testIntBetween()
	{
		$this->assertEquals("`name` BETWEEN 0 AND 1", Condition::between('name', [0, 1]));
	}

	public function testIntNotBetween()
	{
		$this->assertEquals("`name` NOT BETWEEN 0 AND 1", Condition::not_between('name', [0, 1]));
	}

	public function testIntIn()
	{
		$this->assertEquals("`name` IN (0, 1, 2)", Condition::in('name', [0, 1, 2]));
	}

	public function testIntNotIn()
	{
		$this->assertEquals("`name` NOT IN (0, 1, 2)", Condition::not_in('name', [0, 1, 2]));
	}


	public function testDoubleEq()
	{
		$this->assertEquals("`name` = 1.5", Condition::eq('name', 1.5));
	}

	public function testDoubleNeq()
	{
		$this->assertEquals("`name` != 1.5", Condition::neq('name', 1.5));
	}

	public function testDoubleNeqNull()
	{
		$this->assertEquals("(`name` IS NOT NULL AND `name` != 1.5)", Condition::neq('name', [null, 1.5]));
	}

	public function testDoubleLt()
	{
		$this->assertEquals("`name` < 1.5", Condition::lt('name', 1.5));
	}

	public function testDoubleLte()
	{
		$this->assertEquals("`name` <= 1.5", Condition::lte('name', 1.5));
	}

	public function testDoubleGt()
	{
		$this->assertEquals("`name` > 1.5", Condition::gt('name', 1.5));
	}

	public function testDoubleGte()
	{
		$this->assertEquals("`name` >= 1.5", Condition::gte('name', 1.5));
	}

	public function testDoubleBetween()
	{
		$this->assertEquals("`name` BETWEEN 1.5 AND 3.2", Condition::between('name', [1.5, 3.2]));
	}

	public function testDoubleNotBetween()
	{
		$this->assertEquals("`name` NOT BETWEEN 1.5 AND 3.2", Condition::not_between('name', [1.5, 3.2]));
	}

	public function testDoubleIn()
	{
		$this->assertEquals("`name` IN (1.5, 2.5, 10.9)", Condition::in('name', [1.5, 2.5, 10.9]));
	}

	public function testDoubleNotIn()
	{
		$this->assertEquals("`name` NOT IN (1.5, 2.5, 10.9)", Condition::not_in('name', [1.5, 2.5, 10.9]));
	}

	public function testPrepareValueString()
	{
		$this->assertEquals("'a'", Condition::prepare_value('a'));
		$this->assertEquals("'a\'a'", Condition::prepare_value('a\'a'));
	}

	public function testPrepareValueInt()
	{
		$this->assertEquals("1", Condition::prepare_value(1));
		$this->assertEquals("1", Condition::prepare_value('1', ConditionType::INT));
	}

	public function testPrepareValueBit()
	{
		$this->assertEquals("b'1'", Condition::prepare_value(true));
		$this->assertEquals("b'1'", Condition::prepare_value(1, ConditionType::BOOL));
		$this->assertEquals("b'1'", Condition::prepare_value('true', ConditionType::BOOL));
		//$this->assertEquals("b'1'", Condition::prepare_value('on', ConditionType::BOOL));
		$this->assertEquals("b'1'", Condition::prepare_value('yes', ConditionType::BOOL));

		$this->assertEquals("b'0'", Condition::prepare_value(false));
		$this->assertEquals("b'0'", Condition::prepare_value(0, ConditionType::BOOL));
		$this->assertEquals("b'0'", Condition::prepare_value('false', ConditionType::BOOL));
		//$this->assertEquals("b'0'", Condition::prepare_value('off', ConditionType::BOOL));
		$this->assertEquals("b'0'", Condition::prepare_value('no', ConditionType::BOOL));
	}

	public function testPrepareValueDouble()
	{
		$this->assertEquals("0.35", Condition::prepare_value(.35));
		$this->assertEquals("0.35", Condition::prepare_value('.35', ConditionType::DOUBLE));
	}

	public function testPrepareValueDate()
	{
		date_default_timezone_set('UTC');
		$this->assertEquals("'2018-05-01'", Condition::prepare_value(new DateTime('2018-05-01T01:00:00+02:00'), ConditionType::DATE));
		$this->assertEquals("'2018-05-01'", Condition::prepare_value(strtotime('2018-05-01T03:00:00+02:00'), ConditionType::DATE));
		$this->assertEquals("'2018-05-01'", Condition::prepare_value('2018-05-01', ConditionType::DATE));
	}

	public function testPrepareValueDateTime()
	{
		date_default_timezone_set('UTC');
		$this->assertEquals("'2018-05-01T10:20:30+02:00'", Condition::prepare_value(new DateTime('2018-05-01T10:20:30+02:00'), ConditionType::DATETIME));
		$this->assertEquals("'2018-05-01T10:20:30+00:00'", Condition::prepare_value(strtotime('2018-05-01T12:20:30+02:00'), ConditionType::DATETIME));
		$this->assertEquals("'2018-05-01T10:20:30+02:00'", Condition::prepare_value('2018-05-01T10:20:30+02:00', ConditionType::DATETIME));
	}

	public function testPrepareValueTimestamp()
	{
		date_default_timezone_set('UTC');
		$this->assertEquals("'2018-05-01T10:20:30+02:00'", Condition::prepare_value(new DateTime('2018-05-01T10:20:30+02:00'), ConditionType::TIMESTAMP));
		$this->assertEquals("'2018-05-01T10:20:30+00:00'", Condition::prepare_value(strtotime('2018-05-01T12:20:30+02:00'), ConditionType::TIMESTAMP));
		$this->assertEquals("'2018-05-01T10:20:30+02:00'", Condition::prepare_value('2018-05-01T10:20:30+02:00', ConditionType::TIMESTAMP));
	}

	public function testPrepareValueTime()
	{
		date_default_timezone_set('UTC');
		$this->assertEquals("'10:20:30+02:00'", Condition::prepare_value(new DateTime('2018-05-01T10:20:30+02:00'), ConditionType::TIME));
		$this->assertEquals("'10:20:30+00:00'", Condition::prepare_value(strtotime('2018-05-01T12:20:30+02:00'), ConditionType::TIME));
		$this->assertEquals("'10:20:30+02:00'", Condition::prepare_value('10:20:30+02:00', ConditionType::TIME));
	}
}