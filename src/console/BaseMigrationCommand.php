<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 10:50
 */

namespace Sllite\console;

use Symfony\Component\Console\Command\Command;

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
     */
    protected function addMigrationInfo($migrationName)
    {
        $sql = 'INSERT INTO `' . $this->config['migration_table_name'] . '` (`name`) VALUES (:name)';

        $statement = $this->db->prepare($sql);
        $statement->bindParam('name', $migrationName);

        $result = $statement->execute();
        if (!$result) {
            throw new \RuntimeException('Ошибка фиксации миграции');
        }

        return $result;
    }
}
