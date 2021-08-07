<?php

namespace Codememory\Components\Database\Migrations\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\Database\Migrations\MigrationCreator;
use Codememory\Components\Database\Migrations\Utils as MigrationUtils;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateMigrationCommand
 *
 * @package Codememory\Components\Database\Migrations\Commands
 *
 * @author  Codememory
 */
class CreateMigrationCommand extends Command
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'migrations:create';

    /**
     * @inheritDoc
     */
    protected ?string $description = 'Create migration';

    /**
     * @inheritDoc
     */
    protected function wrapArgsAndOptions(): Command
    {

        $this->addArgument('name', InputArgument::REQUIRED, 'Migrator name');

        return $this;

    }

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $migrationUtils = new MigrationUtils();
        $migrationCreator = new MigrationCreator($migrationUtils);
        $migrationName = $input->getArgument('name');

        if ($migrationCreator->existMigrationByName($migrationName)) {
            $this->io->error(sprintf('A migration named %s already exists', $migrationName));

            return Command::FAILURE;
        }

        $createdMigrationData = $migrationCreator->create($migrationName);

        $this->io->success('Migrator has been successfully created');

        $this->io->text(sprintf(
            '%s: %s',
            $this->tags->blueText('created'),
            $this->tags->yellowText($createdMigrationData['path'])
        ));

        return Command::SUCCESS;

    }

}