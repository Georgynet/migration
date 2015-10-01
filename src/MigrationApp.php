<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 18.08.15
 * Time: 14:53
 */

namespace Sllite;

use Sllite\console\ApplyMigrationCommand;
use Sllite\console\InitMigrationCommand;
use Sllite\console\ListMigrationCommand;
use Sllite\console\NewMigrationCommand;
use Sllite\console\RevertMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

/**
 * Приложение для управления миграциями.
 */
class MigrationApp
{
    /**
     * @var Application консольное прилоежние
     */
    private $console;
    /**
     * @var array массив настроек
     */
    private $config;
    /**
     * @var \PDO подключение к БД
     */
    private $db;

    /**
     * Конструктор.
     * @param Application $console консольное приложение
     * @param \PDO $db подключение к БД
     * @param array $config конфигурационный файл
     */
    public function __construct(Application $console, \PDO $db, array $config)
    {
        $this->console = $console;
        $this->db = $db;
        $this->config = $config;

        $this->addMethods();
    }

    /**
     * Добавляет список команд в консольное приложение.
     */
    protected function addMethods()
    {
        $this->console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));

        /** @var array $config */
        $this->console->add(new InitMigrationCommand($this->config, $this->db));
        $this->console->add(new NewMigrationCommand($this->config));
        $this->console->add(new ListMigrationCommand($this->config, $this->db));
        $this->console->add(new ApplyMigrationCommand($this->config, $this->db));
        $this->console->add(new RevertMigrationCommand($this->config, $this->db));
    }
}
