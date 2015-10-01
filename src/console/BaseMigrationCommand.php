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
}
