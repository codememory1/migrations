<?php

namespace Codememory\Components\Database\Migrations\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\Database\Migrations\Executor;
use Codememory\Components\Database\Migrations\MigrationData;
use Codememory\Components\Database\Migrations\Migrator;
use Codememory\Components\Database\Migrations\Utils as MigrationUtils;
use Codememory\Components\Database\Orm\Commands\AbstractCommand;
use Codememory\Components\Database\QueryBuilder\Exceptions\NotSelectedStatementException;
use Codememory\Components\DateTime\Exceptions\InvalidTimezoneException;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateCommand
 *
 * @package Codememory\Components\Database\Migrations\Commands
 *
 * @author  Codememory
 */
class MigrateCommand extends AbstractCommand
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'migrations:migrate';

    /**
     * @inheritDoc
     */
    protected ?string $description = 'Perform one or all migrations';

    /**
     * @inheritDoc
     */
    protected function wrapArgsAndOptions(): Command
    {

        $this->addOption('name', null, InputOption::VALUE_NONE, 'Perform mirage by name');

        return $this;

    }

    /**
     * @inheritDoc
     * @throws InvalidTimezoneException
     * @throws NotSelectedStatementException
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        if (false === $this->isConnection($this->connector)) {
            $this->checkConnection();

            return Command::FAILURE;
        }

        $migrationUtils = new MigrationUtils();
        $migrationData = new MigrationData($migrationUtils);
        $executor = new Executor($this->connector);
        $dataStartMigrate = [
            'migrations'     => 0,
            'queries'        => 0,
            'execution-time' => 0
        ];

        if ($input->getOption('name')) {
            $migrationBundles = $migrationData->getMigrationNames();

            $migrationName = $this->io->askWithAutocomplete('Enter the name of the migration', $migrationBundles, null, function (mixed $name) use ($migrationBundles) {
                if (!in_array($name, $migrationBundles)) {
                    throw new RuntimeException(sprintf('There is no migration named %s', $name));
                }

                return $name;
            });

            $migrationData = $migrationData->getMigrationFileDataByName($migrationName);

            $this->migrate($migrationData, $executor, $dataStartMigrate);
        } else {
            foreach ($migrationData->sortMigrationsByDate($migrationData->getMigrationFilesData()) as $migrationData) {
                $this->migrate($migrationData, $executor, $dataStartMigrate);
            }
        }

        $this->io->text(sprintf(
            $this->tags->greenText('Migrations completed %s requests %s execution time %s ms'),
            $this->tags->yellowText($dataStartMigrate['migrations']),
            $this->tags->yellowText($dataStartMigrate['queries']),
            $this->tags->yellowText($dataStartMigrate['execution-time'])
        ));

        return Command::SUCCESS;

    }

    /**
     * @param array    $migrationData
     * @param Executor $executor
     * @param array    $dataStartMigrate
     *
     * @throws InvalidTimezoneException
     * @throws NotSelectedStatementException
     */
    private function migrate(array $migrationData, Executor $executor, array &$dataStartMigrate): void
    {

        $namespaceMigration = $migrationData['full-namespace'];
        $migrator = new Migrator(new $namespaceMigration());

        $dataMigrate = $executor->exec($migrationData, $migrator->getUp());

        if (false !== $dataMigrate) {
            $dataStartMigrate['migrations'] += 1;
            $dataStartMigrate['queries'] += $dataMigrate['count-queries'];
            $dataStartMigrate['execution-time'] += $dataMigrate['execution-time'];
        }

    }

}