<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 02.10.15
 * Time: 14:55
 */

namespace Sllite\tests;

use Sllite\console\ApplyMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class UpMigrationCommandTest extends BaseMigrationCommandTest
{
    /**
     * @dataProvider useMigrations
     * @param string $query текст запроса
     * @param string $method название контроллирующего метода
     */
    public function testExecute($query, $method)
    {
        $application = new Application();

        $application->add(new ApplyMigrationCommand(
            self::getConfig(),
            self::$dbh
        ));

        $command = $application->find('migrate:up');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['name' => 1]);

        $this->assertTrue($this->$method($query));
    }
}