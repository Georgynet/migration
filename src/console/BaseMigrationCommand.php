<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 10:50
 */

namespace Sllite\console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Базовый класс консольных комманд миграции.
 */
abstract class BaseMigrationCommand extends Command
{
    /**
     * Префикс миграций
     */
    const PREFIX_MIGRATION_NAME = 'm_';

    /**
     * @var array конфигурационный файл
     */
    protected $config;
    /**
     * @var \PDO $db подключение к БД
     */
    protected $db;
    /**
     * @var array список примененных миграций
     */
    static protected $appliedMigration = [];

    /**
     * {@inheritdoc}
     * @param array $config конфигурационный файл
     */
    public function __construct($config = [], \PDO $db = null)
    {
        $this->config = $config;
        $this->db = $db;

        parent::__construct();
    }

    /**
     * Выполняет запрос миграцию.
     * @param string $migrationName имя миграции
     * @param string $query запрос
     * @return bool|\Exception|\PDOException
     * @throw \RuntimeException в случае, если не удалось применить
     */
    protected function runMigration($migrationName, $query)
    {
        if (empty($query)) {
            throw new \RuntimeException('В миграции отсутствует запрос');
        }

        try {
            $this->db->beginTransaction();

            $statement = $this->db->prepare($query);
            $result = $statement->execute();

            if (!$result) {
                throw new \RuntimeException('Запрос не был выполнен');
            }

            $this->addMigrationInfo($migrationName);

            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollBack();

            return $e;
        }

        return true;
    }

    /**
     * Добавляет информацию о примененной миграции.
     * @param string $migrationName имя фиксируемой миграции
     * @return bool
     */
    protected function addMigrationInfo($migrationName)
    {
        $statement = $this->db->prepare(
            'INSERT INTO `' . $this->config['migration_table_name'] . '` (`name`) VALUES (:name)'
        );
        $statement->bindParam('name', $migrationName);

        $result = $statement->execute();
        if (!$result) {
            throw new \RuntimeException('Ошибка фиксации миграции');
        }

        return $result;
    }

    /**
     * Возвращает список зафиксированных миграций.
     * @return array
     */
    protected function getAppliedMigration()
    {
        $statement = $this->db->prepare(
            'SELECT `name` FROM `' . $this->config['migration_table_name']
        );

        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $migrations = [];
        foreach ($result as $migration) {
            $migrations[$migration['name']] = 1;
        }

        return $migrations;
    }

    /**
     * Добавяет миграцию в список зафиксированных.
     * @param string $migrationName имя миграции
     */
    protected function setAppliedMigration($migrationName)
    {
        self::$appliedMigration[$migrationName] = 1;
    }

    /**
     * Проверяет является ли класс миграцией.
     * @param SplFileInfo $file файл
     * @return bool|IMigration
     */
    protected function isMigration(SplFileInfo $file)
    {
        if (empty($appliedMigration)) {
            self::$appliedMigration = $this->getAppliedMigration();
        }

        if (isset(self::$appliedMigration[$file->getBasename('.php')])) {
            return false;
        }

        require $file->getPathname();

        $className = $file->getBasename('.php');
        $migration = new $className();

        if (!$migration instanceof IMigration) {
            return false;
        }

        return $migration;
    }
}
