<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 02.10.15
 * Time: 14:23
 */

namespace Sllite\tests;

use Sllite\console\NewMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class NewMigrationCommandTest extends BaseMigrationCommandTest
{
    public function testExecute()
    {
        $application = new Application();

        $application->add(new NewMigrationCommand(
            self::getConfig()
        ));

        $command = $application->find('migrate:new');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertRegExp('/Создана миграция: m_\d+_\d+/', $commandTester->getDisplay());

        $migrationName = trim(explode(': ', $commandTester->getDisplay())[1]);

        $this->assertTrue(
            file_exists($this->getConfig()['migration_path'] . '/' . $migrationName . '.php')
        );
    }
}
