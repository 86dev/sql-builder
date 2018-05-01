<?php

use PHPUnit\Framework\TestCase;
use SQLBuilder\SQL;

class CreateTableTest extends TestCase
{
	public function testCreateTable()
	{
		$table = SQL::create_table('users')->ifnotexists();
		$table->int('id')->unsigned()->auto_increment()->primary();
		$table->varchar('email')->length(190)->unique();
		$table->enum('gender')->values(['F', 'M'])->_default('F')->default_quoted();
		$table->int('company_id')->nullable()->unsigned();
		$table->index(['last_name', 'first_name']);
		$table->foreign_key('company_id')->on('companies')->references('id');

		$this->assertEquals("CREATE TABLE IF NOT EXISTS `users` ("
				."`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,"
				." `email` VARCHAR(190) NOT NULL UNIQUE,"
				." `gender` ENUM ('F', 'M') NOT NULL DEFAULT 'F',"
				." `company_id` INT UNSIGNED NULL,"
				." INDEX `users_last_name_first_name_index` (`last_name`, `first_name`),"
				." CONSTRAINT `users_company_id_foreign_key` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`)"
			.") ENGINE = InnoDB",
			$table->parse_query()
		);
	}
}