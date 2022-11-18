<?php

use SQLBuilder\SQL;
use SQLBuilder\Condition;
use PHPUnit\Framework\TestCase;
use SQLBuilder\Enums\ConditionType;
use SQLBuilder\Statements\Conditions;
use SQLBuilder\Enums\ConditionOperation;

class ConditionsTest extends TestCase
{
	public function testString()
	{
		$this->assertEquals("`name` = 'test'", Conditions::create("`name` = 'test'")->parse_query());
	}

	public function testStrings()
	{
		$this->assertEquals("(`name` = 'test' OR `id` = 1)", Conditions::create(['relation' => 'OR', "`name` = 'test'", "`id` = 1"])->parse_query());
	}

	public function testStringsWithoutRelation()
	{
		$this->assertEquals("(`name` = 'test' AND `id` = 1)", Conditions::create(["`name` = 'test'", "`id` = 1"])->parse_query());
	}

	public function testNestedStrings()
	{
		$this->assertEquals("(`name` = 'test' OR (`status` IS NULL AND `active` = 1))", Conditions::create([
			'relation' => 'OR',
			"`name` = 'test'",
			[
				'relation' => 'AND',
				"`status` IS NULL",
				"`active` = 1"
			]
		])->parse_query());
	}

	public function testNestedStringsWithoutRelation()
	{
		$this->assertEquals("(`name` = 'test' AND (`status` IS NULL AND `active` = 1))", Conditions::create([
			"`name` = 'test'",
			[
				"`status` IS NULL",
				"`active` = 1"
			]
		])->parse_query());
	}

	public function testCondition()
	{
		$this->assertEquals("`name` = 'test'", Conditions::create(Condition::eq("name", 'test'))->parse_query());
	}

	public function testConditions()
	{
		$this->assertEquals("(`name` = 'test' OR `id` = 1)", Conditions::create([
			'relation' => 'OR',
			Condition::eq("name", 'test'),
			Condition::eq('id', 1)
		])->parse_query());
	}

	public function testConditionsWithoutRelation()
	{
		$this->assertEquals("(`name` = 'test' AND `id` = 1)", Conditions::create([
			Condition::eq("name", 'test'),
			Condition::eq('id', 1)
		])->parse_query());
	}

	public function testNestedConditions()
	{
		$this->assertEquals("(`name` = 'test' OR (`status` IS NULL AND `active` = 1))", Conditions::create([
			'relation' => 'OR',
			Condition::eq("name", 'test'),
			[
				'relation' => 'AND',
				Condition::is_null('status'),
				Condition::eq('active', 1)
			]
		])->parse_query());
	}

	public function testNestedConditionsWithoutRelation()
	{
		$this->assertEquals("(`name` = 'test' AND (`status` IS NULL AND `active` = 1))", Conditions::create([
			Condition::eq("name", 'test'),
			[
				Condition::is_null('status'),
				Condition::eq('active', 1)
			]
		])->parse_query());
	}

	public function testNestedConditionsWithColumn()
	{
		$this->assertEquals("(`k`.`id` = 1 AND (`k`.`status` = `p`.`status` OR `k`.`active` = `p`.`active`))", Conditions::create([
			Condition::eq("k.id", 1),
			[
				'relation' => 'OR',
				Condition::column('k.status', 'p.status'),
				Condition::column('k.active', 'p.active')
			]
		])->parse_query());
	}

	public function testArray()
	{
		$this->assertEquals("`name` = 'test'", Conditions::create(['field' => 'name', 'values' => 'test'])->parse_query());
	}

	public function testArrays()
	{
		$this->assertEquals("(`name` = 'test' OR `id` = 1)", Conditions::create([
			'relation' => 'OR',
			['field' => 'name', 'values' => 'test'],
			['field' => 'id', 'values' => 1]
		])->parse_query());
	}

	public function testArraysBool()
	{
		$this->assertEquals("(`active` = b'1' OR `id` = 0)", Conditions::create([
			'relation' => 'OR',
			['field' => 'active', 'values' => true],
			['field' => 'id', 'values' => 0]
		])->parse_query());
	}

	public function testArraysWithoutRelation()
	{
		$this->assertEquals("(`name` = 'test' AND `id` = 1)", Conditions::create([
			['field' => 'name', 'values' => 'test'],
			['field' => 'id', 'values' => 1]
		])->parse_query());
	}

	public function testNestedArrays()
	{
		$this->assertEquals("(`name` = 'test' OR (`status` IS NULL AND `active` = 1))", Conditions::create([
			'relation' => 'OR',
			['field' => "name", 'values' => 'test'],
			[
				'relation' => 'AND',
				['field' => 'status', 'operator' => ConditionOperation::ISNULL],
				['field' => 'active', 'values' => 1]
			]
		])->parse_query());

		$conditions = Conditions::create([
			'relation' => 'OR',
			['field' => "name", 'values' => 'test']
		]);
		$conditions->conditions([
			'relation' => 'AND',
			['field' => 'status', 'operator' => ConditionOperation::ISNULL],
			['field' => 'active', 'values' => 1]
		]);
		$this->assertEquals("(`name` = 'test' OR (`status` IS NULL AND `active` = 1))", $conditions->parse_query());
	}

	public function testNestedArraysWithoutRelation()
	{
		$this->assertEquals("(`name` = 'test' AND (`status` IS NULL AND `active` = 1))", Conditions::create([
			['field' => "name", 'values' => 'test'],
			[
				['field' => 'status', 'operator' => ConditionOperation::ISNULL],
				['field' => 'active', 'values' => 1]
			]
		])->parse_query());

		$conditions = Conditions::create([['field' => "name", 'values' => 'test']]);
		$conditions->conditions([
			['field' => 'status', 'operator' => ConditionOperation::ISNULL],
			['field' => 'active', 'values' => 1]
		]);
		$this->assertEquals("(`name` = 'test' AND `status` IS NULL AND `active` = 1)", $conditions->parse_query());
	}
}