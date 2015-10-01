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
    const PREFIX_MIGRATION_NAME = 'm_';

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
        $migrationName = self::PREFIX_MIGRATION_NAME . date('Y-m-d_H-i-s');

        $name = $input->getArgument('name');
        if ($name) {
            $migrationName = $migrationName .'_' . $name;
        }

        var_dump($this->config);

        $output->writeln($migrationName);
    }
}
