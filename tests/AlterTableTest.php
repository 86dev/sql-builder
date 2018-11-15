<?php

use PHPUnit\Framework\TestCase;
use SQLBuilder\SQL;
use SQLBuilder\Index;
use SQLBuilder\ForeignKey;
use SQLBuilder\Column;
use SQLBuilder\Primary;

class AlterTableTest extends TestCase
{
	public function testAlterTable()
	{
		$table = SQL::alter_table('users')
			->drop_primary()
			->drop_index(Index::create()->name('users_email_unique'))
			->drop_foreign(ForeignKey::create()->name('users_company_id_foreign'))
			->drop_column(Column::int('qty'))
			->add_column(Column::int('id')->unsigned()->auto_increment()->primary()->first())
			->add_column(Column::varchar('test')->length(200)->nullable()->after('name'))
			->change_column(Column::varchar('name')->new_name('login')->length(150)->unique())
			->comment('test commentaire')
			->add_primary(Primary::create()->fields('id'));

		$this->assertEquals("ALTER TABLE `users`"
			." DROP PRIMARY KEY,"
			." DROP INDEX `users_email_unique`,"
			." DROP FOREIGN KEY `users_company_id_foreign`,"
			." DROP COLUMN `qty`,"
			." ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,"
			." ADD COLUMN `test` VARCHAR(200) NULL AFTER `name`,"
			." CHANGE COLUMN `name` `login` VARCHAR(150) NOT NULL UNIQUE,"
			." ADD PRIMARY KEY (`id`)", $table->parse_query());
	}
}