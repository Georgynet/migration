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
                'step',
                InputArgument::OPTIONAL,
                'Количество отменяемых миграций'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $step = (int) $input->getArgument('step');
        if (!is_numeric($step) || 0 === $step) {
            $step = 1;
        }

        $appliedMigrations = $this->getAppliedMigration();

        $i = 0;
        foreach ($appliedMigrations as $appliedMigration => $v) {
            ++$i;

            $finder = new Finder();
            /** @var SplFileInfo $file */
            $file = array_values(iterator_to_array(
                $finder
                    ->name($appliedMigration . '.php')
                    ->in($this->config['migration_path'])
            ))[0];

            if (!$migration = $this->isMigration($file)) {
                continue;
            }

            $className = $file->getBasename('.php');

            $output->writeln(
                'Запущен откат миграции: ' . $className
            );

            $this->runMigration(
                $className,
                $migration->down(),
                'up'
            );

            $this->unsetAppliedMigration($className);

            $output->writeln(
                'Отменена миграция: ' . $className
            );

            if ($step === $i) {
                break;
            }
        }
    }
}
