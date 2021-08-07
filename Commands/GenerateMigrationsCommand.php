<?php

namespace Codememory\Components\Database\Migrations\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\Database\Migrations\MigrationCreator;
use Codememory\Components\Database\Migrations\Utils as MigrationUtils;
use Codememory\Components\Database\Orm\Commands\AbstractCommand;
use Codememory\Components\Database\Orm\Exceptions\ObjectIsNotEntityException;
use Codememory\Components\Database\Orm\SQLBuilder\CreateTableOfEntity;
use Codememory\Components\Database\Orm\SQLBuilder\DropTableOfEntity;
use Codememory\Components\Database\Orm\Utils as OrmUtils;
use Codememory\Components\Finder\Find;
use Codememory\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateMigrationsCommand
 *
 * @package Codememory\Components\Database\Migrations\Commands
 *
 * @author  Codememory
 */
class GenerateMigrationsCommand extends AbstractCommand
{

    /**
     * @inheritDoc
     */
    protected ?string $command = 'migrations:generate';

    /**
     * @inheritDoc
     */
    protected ?string $description = 'Generate migrations from entity';

    /**
     * @inheritDoc
     * @throws ReflectionException
     * @throws ObjectIsNotEntityException
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $finder = new Find();
        $ormUtils = new OrmUtils();
        $migrationUtils = new MigrationUtils();
        $migrationCreator = new MigrationCreator($migrationUtils);
        $entities = $finder
            ->setPathForFind($ormUtils->getPathWithEntities())
            ->file()
            ->byRegex(sprintf('%s\.php$', $ormUtils->getEntitySuffix()))
            ->get();

        $allMigrationsCreated = false;

        foreach ($entities as $entity) {
            $entityFilename = Str::trimToSymbol($entity, '/', false);
            $entityClassName = Str::trimAfterSymbol($entityFilename, '.', false);
            $entityNamespace = $ormUtils->getEntityNamespace() . $entityClassName;
            $migrationName = sprintf('Create%sTable', Str::trimAfterSymbol($entityClassName, $ormUtils->getEntitySuffix(), false));

            if (!$migrationCreator->existMigrationByName($migrationName)) {
                $allMigrationsCreated = true;

                $createdData = $this->create($migrationCreator, $migrationName, $entityNamespace);

                $this->successfulCreation($createdData);

                sleep(1);
            }
        }

        if ($allMigrationsCreated) {
            $this->io->success('Migrations have been successfully generated');
        } else {
            $this->io->warning('All migrations have already been generated');
        }

        return Command::SUCCESS;

    }

    /**
     * @param MigrationCreator $migrationCreator
     * @param string           $migrationName
     * @param string           $entity
     *
     * @return array
     * @throws ObjectIsNotEntityException
     * @throws ReflectionException
     */
    #[ArrayShape(['name' => "string", 'full-name' => "string", 'path' => "string"])]
    private function create(MigrationCreator $migrationCreator, string $migrationName, string $entity): array
    {

        return $migrationCreator->create($migrationName, $this->getUp($entity), $this->getDown($entity));

    }

    /**
     * @param array $createdMigrationData
     *
     * @return void
     */
    private function successfulCreation(array $createdMigrationData): void
    {

        $this->io->text(sprintf(
            '%s: %s',
            $this->tags->blueText('created'),
            $this->tags->yellowText($createdMigrationData['path'])
        ));

    }

    /**
     * @param string $entity
     *
     * @return string
     * @throws ObjectIsNotEntityException
     * @throws ReflectionException
     */
    private function getUp(string $entity): string
    {

        $createTableOfEntity = new CreateTableOfEntity($this->connector, $entity);

        return <<<UP
            \$schema->addSql('{$createTableOfEntity->buildToString()}');
        UP;

    }

    /**
     * @param string $entity
     *
     * @return string
     * @throws ObjectIsNotEntityException
     * @throws ReflectionException
     */
    private function getDown(string $entity): string
    {

        $createTableOfEntity = new DropTableOfEntity($this->connector, $entity);

        return <<<UP
            \$schema->addSql('{$createTableOfEntity->buildToString()}');
        UP;

    }

}