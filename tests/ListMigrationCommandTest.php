<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 02.10.15
 * Time: 9:34
 */

namespace Sllite\tests;

use Sllite\console\ListMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ListMigrationCommandTest extends BaseMigrationCommandTest
{
    /**
     * @dataProvider migrations
     * @param string $query текст запроса
     * @param string $result текст результата
     */
    public function testExecute($query, $result)
    {
        $application = new Application();

        if (!is_null($query)) {
            self::$dbh->exec($query);
        }

        $application->add(new ListMigrationCommand(
            self::getConfig(),
            self::$dbh
        ));

        $command = $application->find('migrate:list');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals($result, $commandTester->getDisplay());
    }
}
