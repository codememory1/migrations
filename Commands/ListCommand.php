<?php

namespace Codememory\Components\Database\Migrations\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\Database\Migrations\MigrationData;
use Codememory\Components\Database\Migrations\Utils as MigrationsUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListCommand
 *
 * @package Codememory\Components\Database\Migrations\Commands
 *
 * @author  Codememory
 */
class ListCommand extends Command
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'migrations:list';

    /**
     * @inheritDoc
     */
    protected ?string $description = 'Get a table of all migrations';

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $migrationsUtils = new MigrationsUtils();
        $migrationFileData = new MigrationData($migrationsUtils);

        $migrationsData = [];

        foreach ($migrationFileData->getMigrationFilesData() as $data) {
            $migrationsData[] = [
                $data['name'],
                $data['full-name'],
                $data['path']
            ];
        }

        $this->io->table(
            ['name', 'full name', 'path'],
            $migrationsData
        );

        return Command::SUCCESS;

    }

}