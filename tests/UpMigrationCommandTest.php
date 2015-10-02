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
     * @dataProvider applyMigrations
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

    private function existTable($query)
    {
        try {
            $statement = self::$dbh->prepare($query);
            $statement->execute();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    private function existRow($query)
    {
        $statement = self::$dbh->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll();

        return (isset($result[0]['cnt']) && 1 === (int) $result[0]['cnt'] );
    }
}