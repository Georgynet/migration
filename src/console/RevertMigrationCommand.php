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
                'Название миграции'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
