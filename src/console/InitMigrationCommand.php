<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 15:22
 */

namespace Sllite\console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Комманда инициализации механизма миграций
 */
class InitMigrationCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this
            ->setName('migrate:init')
            ->setDescription('Инициализация механизма миграций')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $this->config['migration_table_name'] . '`
                (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                 `name` VARCHAR(255) NOT NULL,
                 PRIMARY KEY (`id`)
                ) ENGINE = InnoDB;';

        $statement = $this->db->prepare($sql);
        $statement->bindParam('table_name', $this->config['migration_table_name'], \PDO::PARAM_STR);

        $result = $statement->execute();
        if (!$result) {
            throw new \RuntimeException('Ошибка инициализации');
        }
    }
}
