<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 02.10.15
 * Time: 9:50
 */

namespace Sllite\test;

use PHPUnit_Framework_TestCase;

/**
 * Базовый класс для тестирования консольных комманд.
 */
abstract class BaseMigrationCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PDO $dbh
     */
    protected static $dbh;

    public static function setUpBeforeClass()
    {
        self::$dbh = new \PDO('sqlite::memory:', '', '', array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ));

        self::$dbh->exec(
            file_get_contents(__DIR__ . '/fixtures/fixtures.sql')
        );

        $migrationPath = self::getConfig()['migration_path'];

        if (!file_exists($migrationPath)) {
            mkdir($migrationPath, 0755, true);
        }

        $migrations = [
            'm_1' => [
                'upSql' => 'return \'CREATE TABLE IF NOT EXISTS `table_test` (`id` INT UNSIGNED NOT NULL, `name` VARCHAR(255) NOT NULL, PRIMARY KEY (`id`));\';'
            ],
            'm_2' => [
                'upSql' => 'return \'INSERT INTO `table_test` (`id`, `name`) VALUES (1, "m_1");\';'
            ],
            'm_3' => [
                'upSql' => 'return \'INSERT INTO `table_test` (`id`, `name`) VALUES (2, "m_2");\';'
            ]
        ];

        foreach ($migrations as $migrationName => $migrationValue) {
            extract($migrationValue);
            $migrationFile = include __DIR__ . '/fixtures/migrationTemplate.php';
            file_put_contents(
                $migrationPath . '/' . $migrationName . '.php',
                $migrationFile
            );
        }
    }

    public static function tearDownAfterClass()
    {
        self::$dbh = NULL;

        $migrationPath = self::getConfig()['migration_path'];

        if (file_exists($migrationPath)) {
            self::rmDirTree($migrationPath);
        }
    }

    /**
     * Возвращает конфиг.
     * @return array
     */
    public static function getConfig()
    {
        return [
            'migration_path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'migrationsTest',
            'migration_table_name' => 'migration_table'
        ];
    }

    /**
     * Удаляет директорию с файлами.
     * @param string $dir путь до директории
     * @return bool
     */
    public static function rmDirTree($dir) {
        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::rmDirTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    public static function migrations()
    {
        return [
            [
                'query' => null,
                'result' => "m_1\nm_2\nm_3\n",
            ],
            [
                'query' => 'INSERT INTO `migration_table` (`id`, `name`) VALUES (1, "m_1");',
                'result' => "m_2\nm_3\n",
            ],
            [
                'query' => 'INSERT INTO `migration_table` (`id`, `name`) VALUES (2, "m_2");',
                'result' => "m_3\n",
            ],
            [
                'query' => 'INSERT INTO `migration_table` (`id`, `name`) VALUES (3, "m_3");',
                'result' => '',
            ],
            [
                'query' => 'DELETE FROM `migration_table` WHERE `id` = 3;',
                'result' => "m_3\n",
            ],
            [
                'query' => 'DELETE FROM `migration_table`;',
                'result' => "m_1\nm_2\nm_3\n",
            ]
        ];
    }

    public static function applyMigrations()
    {
        return [
            [
                'query' => 'SELECT * FROM `table_test`;',
                'method' => 'existTable'
            ],
            [
                'query' => 'SELECT count(1) `cnt` FROM `table_test` WHERE `name` = "m_1";',
                'method' => 'existRow'
            ],
            [
                'query' => 'SELECT count(1) `cnt` FROM `table_test` WHERE `name` = "m_2";',
                'method' => 'existRow'
            ]
        ];
    }
}