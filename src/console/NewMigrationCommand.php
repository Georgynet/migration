<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 10:23
 */

namespace Sllite\console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Консольная команда для создания новой миграции.
 */
class NewMigrationCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this
            ->setName('migrate:new')
            ->setDescription('Создаёт новую миграцию')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Название миграции'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationName = BaseMigrationCommand::PREFIX_MIGRATION_NAME . date('Ymd_His');

        $name = $input->getArgument('name');
        if ($name) {
            $migrationName = $migrationName .'_' . $name;
        }

        if (!file_exists($this->config['migration_path'])) {
            mkdir($this->config['migration_path'], 0755, true);
        }

        $migrationClass = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'migration.php';

        file_put_contents(
            $this->config['migration_path'] . DIRECTORY_SEPARATOR . $migrationName . '.php',
            $migrationClass
        );

        $output->writeln('Создана миграция: ' . $migrationName);
    }
}
