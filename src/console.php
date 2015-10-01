<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 18.08.15
 * Time: 14:53
 */

namespace Sllite;

use Sllite\console\ListMigrationCommand;
use Sllite\console\NewMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('MigrationApp', '1.0');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));

/** @var array $config */
$console->add(new NewMigrationCommand($config));
$console->add(new ListMigrationCommand($config));

return $console;
