<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 12:46
 */

namespace Sllite\console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Возвращает список доступных миграций.
 */
class ListMigrationCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this
            ->setName('migrate:list')
            ->setDescription('Возвращает список доступных миграций')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!file_exists($this->config['migration_path'])) {
            throw new \RuntimeException('Не найдена дирректория с миграциями');
        }

        $finder = new Finder();
        $finder
            ->name(BaseMigrationCommand::PREFIX_MIGRATION_NAME . '*' . '.php')
            ->in($this->config['migration_path'])
            ->sortByName();
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $output->writeln(
                $file->getBasename('.php')
            );
        }
    }
}
