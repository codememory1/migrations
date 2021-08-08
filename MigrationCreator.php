<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Database\Migrations\Interfaces\MigrationDataInterface;
use Codememory\Components\Database\Migrations\Utils as MigrationUtils;
use Codememory\FileSystem\File;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * Class MigrationCreator
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
class MigrationCreator
{

    /**
     * @var MigrationUtils
     */
    private MigrationUtils $migrationUtils;

    /**
     * @var MigrationDataInterface
     */
    private MigrationDataInterface $migrationData;

    /**
     * @param Utils $migrationUtils
     */
    #[Pure]
    public function __construct(MigrationUtils $migrationUtils)
    {

        $this->migrationUtils = $migrationUtils;
        $this->migrationData = new MigrationData($migrationUtils);

    }

    /**
     * @param string $migrationName
     *
     * @return bool
     */
    public function existMigrationByName(string $migrationName): bool
    {

        $migrationFileData = $this->migrationData->getMigrationFileDataByName($migrationName);

        return [] !== $migrationFileData;

    }

    /**
     * @param string      $migrationName
     * @param string|null $up
     * @param string|null $down
     *
     * @return array
     */
    #[ArrayShape(['name' => "string", 'full-name' => "string", 'path' => "string"])]
    public function create(string $migrationName, ?string $up = null, ?string $down = null): array
    {

        $filesystem = new File();
        $migrationFullName = $this->migrationData->generateMigrationFullName($migrationName);
        $pathWithMigrations = $this->migrationUtils->getPathWithMigrations();
        $stubMigration = $this->buildStubMigration(
            $filesystem,
            Str::trimAfterSymbol($this->migrationUtils->getNamespaceMigration(), '\\', false),
            $migrationFullName,
            $up,
            $down
        );
        $pathCreatedMigration = sprintf('%s%s.php', $pathWithMigrations, $migrationFullName);

        if (!$filesystem->exist($pathWithMigrations)) {
            $filesystem->mkdir($pathWithMigrations, 0777, true);
        }

        file_put_contents($pathCreatedMigration, $stubMigration);

        $filesystem->setPermission($pathCreatedMigration);

        return [
            'name'      => $migrationName,
            'full-name' => $migrationFullName,
            'path'      => $pathCreatedMigration
        ];

    }

    /**
     * @param FileInterface $filesystem
     * @param string        $namespace
     * @param string        $migrationFullName
     * @param string|null   $up
     * @param string|null   $down
     *
     * @return string
     */
    private function buildStubMigration(FileInterface $filesystem, string $namespace, string $migrationFullName, ?string $up = null, ?string $down = null): string
    {

        $stub = $this->migrationStub($filesystem);

        return str_replace([
            '{namespace}',
            '{name}',
            '{up}',
            '{down}'
        ], [
            $namespace,
            $migrationFullName,
            $up,
            $down
        ], $stub);

    }

    /**
     * @param FileInterface $filesystem
     *
     * @return string
     */
    private function migrationStub(FileInterface $filesystem): string
    {

        return file_get_contents($filesystem->getRealPath('vendor/codememory/migrations/Commands/Stubs/MigrationStub.stub'));

    }

}