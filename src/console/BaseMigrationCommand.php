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
    protected $appliedMigration = [];

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
     * @param string $type тип миграции
     * @return bool|\Exception|\PDOException
     * @throw \RuntimeException в случае, если не удалось применить
     */
    protected function runMigration($migrationName, $query, $type = 'up')
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

            $statement->closeCursor();

            if ($type == 'up') {
                $this->addMigrationInfo($migrationName);
            } else {
                $this->removeMigrationInfo($migrationName);
            }

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
        return $this->updateMigrationInfo(
            'INSERT INTO `' . $this->config['migration_table_name'] . '` (`name`) VALUES (:name)',
            $migrationName,
            'Ошибка фиксации миграции'
        );
    }

    /**
     * Удаляет информацию о примененной ранее миграции.
     * @param string $migrationName имя удаляемой миграции
     * @return bool
     */
    private function removeMigrationInfo($migrationName)
    {
        return $this->updateMigrationInfo(
            'DELETE FROM `' . $this->config['migration_table_name'] . '` WHERE `name` = :name',
            $migrationName,
            'Ошибка удаления миграции'
        );
    }

    /**
     * Обновляет данные о миграции
     * @param string $query запрос
     * @param string $migrationName имя миграции
     * @param string $message сообщение об ошибке
     * @return bool
     */
    private function updateMigrationInfo($query, $migrationName, $message)
    {
        $statement = $this->db->prepare($query);
        $statement->bindParam('name', $migrationName);

        $result = $statement->execute();
        if (!$result) {
            throw new \RuntimeException($message);
        }

        return $result;
    }

    /**
     * Возвращает список зафиксированных миграций.
     * @param string $order направление сортировки
     * @return array
     */
    protected function getAppliedMigration($order = 'ASC')
    {
        if (!empty($this->appliedMigration)) {
            return $this->appliedMigration;
        }

        $statement = $this->db->prepare(
            'SELECT `name` FROM `' . $this->config['migration_table_name'] . '` ORDER BY `id` ' . $order
        );

        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($result as $migration) {
            $this->appliedMigration[$migration['name']] = 1;
        }

        return $this->appliedMigration;
    }

    /**
     * Добавяет миграцию в список зафиксированных.
     * @param string $migrationName имя миграции
     */
    protected function setAppliedMigration($migrationName)
    {
        $this->appliedMigration[$migrationName] = 1;
    }

    /**
     * Удаляет миграцию из список зафиксированных.
     * @param string $migrationName имя миграции
     */
    protected function unsetAppliedMigration($migrationName)
    {
        if (isset($this->appliedMigration[$migrationName])) {
            unset($this->appliedMigration[$migrationName]);
        }
    }

    /**
     * Проверяет является ли класс миграцией.
     * @param SplFileInfo $file файл
     * @return bool|IMigration
     */
    protected function isMigration(SplFileInfo $file)
    {
        require_once $file->getPathname();

        $className = $file->getBasename('.php');
        $migration = new $className();

        if (!$migration instanceof IMigration) {
            return false;
        }

        return $migration;
    }
}
