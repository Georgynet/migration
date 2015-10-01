<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 16:27
 */

namespace Sllite\console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Консольная команда отменяющая миграцию.
 */
class RevertMigrationCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this
            ->setName('migrate:down')
            ->setDescription('Отменяет миграцию')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Количество отменяемых миграций'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if (is_numeric($name)) {
            $name = (int) $name;
        }

        $appliedMigrations = $this->getAppliedMigration('DESC');

        $i = 0;
        foreach ($appliedMigrations as $appliedMigration => $v) {

            $finder = new Finder();
            /** @var SplFileInfo $file */
            $file = array_values(iterator_to_array(
                $finder
                    ->name($appliedMigration . '.php')
                    ->in($this->config['migration_path'])
            ))[0];

            $appliedMigration = $this->getAppliedMigration();
            if (!isset($appliedMigration[$file->getBasename('.php')])) {
                continue;
            }

            if (!$migration = $this->isMigration($file)) {
                continue;
            }

            ++$i;

            $className = $file->getBasename('.php');

            $output->writeln(
                'Запущен откат миграции: ' . $className
            );

            $this->runMigration(
                $className,
                $migration->down(),
                'down'
            );

            $this->unsetAppliedMigration($className);

            $output->writeln(
                'Отменена миграция: ' . $className
            );

            if (!is_null($name) && ($className == $name || $name === $i)) {
                break;
            }
        }
    }
}
