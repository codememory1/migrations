<?php

namespace Codememory\Components\Database\Migrations\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\Database\Migrations\MigrationData;
use Codememory\Components\Database\Migrations\MigrationRepository;
use Codememory\Components\Database\Migrations\Utils as MigrationsUtils;
use Codememory\Components\Database\Orm\Commands\AbstractCommand;
use Codememory\Components\Database\QueryBuilder\Exceptions\NotSelectedStatementException;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InfoCommand
 *
 * @package Codememory\Components\Database\Migrations\Commands
 *
 * @author  Codememory
 */
class InfoCommand extends AbstractCommand
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'migrations:info';

    /**
     * @inheritDoc
     */
    protected ?string $description = 'Get migration information';

    /**
     * @inheritDoc
     */
    protected function wrapArgsAndOptions(): Command
    {

        $this->addOption('all', null, InputOption::VALUE_NONE, 'Show information about all migrations');

        return $this;

    }

    /**
     * @inheritDoc
     * @throws NotSelectedStatementException
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $migrationRepository = new MigrationRepository($this->connector);
        $migrationsUtils = new MigrationsUtils();
        $migrationFileData = new MigrationData($migrationsUtils);
        $migrationBundles = $migrationFileData->getMigrationNames();

        if (!$input->getOption('all')) {
            $migrationName = $this->io->askWithAutocomplete('Enter the name of the migration', $migrationBundles, null, function (mixed $name) use ($migrationBundles) {
                if (!in_array($name, $migrationBundles)) {
                    throw new RuntimeException(sprintf('There is no migration named %s', $name));
                }

                return $name;
            });
            $records = $migrationRepository->getRecordByName($migrationFileData->getKeyValueByName($migrationName, 'full-name') ?? '');

            if ([] === $migrationFileData->getMigrationFileDataByName($migrationName) || [] === $records) {
                $this->io->warning('No information on this migration');

                return Command::INVALID;
            }
        } else {
            $records = $migrationRepository->getRecords();
        }

        $records = array_map(function (array $data) {
            $data['execution_time'] = sprintf('%s ms', $data['execution_time']);

            return $data;
        }, $records);

        $this->io->table(
            ['full name', 'executed at', 'execution time'],
            $records
        );

        return Command::SUCCESS;

    }

}