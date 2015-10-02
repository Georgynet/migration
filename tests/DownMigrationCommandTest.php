<?php
/**
 * Created by PhpStorm.
 * User: georgy
 * Date: 02.10.15
 * Time: 21:05
 */

namespace Sllite\tests;

use Sllite\console\RevertMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DownMigrationCommandTest extends BaseMigrationCommandTest
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$dbh->exec(
            'INSERT INTO `migration_table` (`id`, `name`) VALUES (1, "m_1");
            INSERT INTO `migration_table` (`id`, `name`) VALUES (2, "m_2");
            INSERT INTO `migration_table` (`id`, `name`) VALUES (3, "m_3");'
        );
    }

    /**
     * @dataProvider useMigrations
     * @param string $query текст запроса
     * @param string $method название контроллирующего метода
     */
    public function testExecute($query, $method)
    {
        $application = new Application();

        $application->add(new RevertMigrationCommand(
            self::getConfig(),
            self::$dbh
        ));

        $command = $application->find('migrate:down');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['name' => 1]);

        $this->assertTrue($this->$method($query));
    }
}