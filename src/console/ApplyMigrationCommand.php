<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 13:09
 */

namespace Sllite\console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Применяет миграцию.
 */
class ApplyMigrationCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this
            ->setName('migrate:up')
            ->setDescription('Применяет миграции')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Название миграции'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder
            ->name(BaseMigrationCommand::PREFIX_MIGRATION_NAME . '*' . '.php')
            ->in($this->config['migration_path'])
            ->sortByName();

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            require $file->getPathname();

            $className = $file->getBasename('.php');
            $migration = new $className();

            if (!$migration instanceof IMigration) {
                continue;
            }

            $output->writeln(
                'Запущена миграция: ' . $className
            );

            $this->runMigration(
                $className,
                $migration->up()
            );

            $output->writeln(
                'Применена миграция: ' . $className
            );
        }
    }
}
