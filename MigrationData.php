<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Database\Migrations\Interfaces\MigrationDataInterface;
use Codememory\Components\Database\Migrations\Utils as MigrationUtils;
use Codememory\Components\Finder\Find;
use Codememory\Support\Str;

/**
 * Class MigrationData
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
class MigrationData implements MigrationDataInterface
{

    private const MIGRATION_FILE_PREFIX = 'Migration';

    /**
     * @var MigrationUtils
     */
    private MigrationUtils $migrationUtils;

    /**
     * @param Utils $migrationUtils
     */
    public function __construct(MigrationUtils $migrationUtils)
    {

        $this->migrationUtils = $migrationUtils;

    }

    /**
     * @inheritDoc
     */
    public function getMigrationFilesData(): array
    {

        $migrationFiles = $this->getMigrationFiles();
        $data = [];

        foreach ($migrationFiles as $migrationFile) {
            $migrationName = $this->getNameMigration($migrationFile);

            $data[] = [
                'filename'       => $this->getFilenameMigration($migrationFile),
                'full-name'      => $this->getFullNameMigration($migrationFile),
                'name'           => $migrationName,
                'path'           => $migrationFile,
                'full-namespace' => $this->migrationUtils->getNamespaceMigration() . $this->getFullNameMigration($migrationFile),
                'namespace'      => $this->migrationUtils->getNamespaceMigration()
            ];
        }

        return $data;

    }

    /**
     * @inheritDoc
     */
    public function getMigrationFileDataByName(string $migrationName): array
    {

        foreach ($this->getMigrationFilesData() as $data) {
            if ($data['name'] === $migrationName) {
                return $data;
            }
        }

        return [];

    }

    /**
     * @inheritDoc
     */
    public function getKeyValueByName(string $migrationName, string $key): mixed
    {

        foreach ($this->getMigrationFilesData() as $data) {
            if ($data['name'] === $migrationName) {
                return $data[$key];
            }
        }

        return null;

    }

    /**
     * @inheritDoc
     */
    public function getMigrationFiles(): array
    {

        $finder = new Find();

        $finder
            ->setPathForFind($this->migrationUtils->getPathWithMigrations())
            ->file()
            ->byRegex(sprintf('%s[0-9]+[A-Za-z0-9]+\.php$', self::MIGRATION_FILE_PREFIX));

        return $finder->get();

    }

    /**
     * @inheritDoc
     */
    public function getMigrationNames(): array
    {

        $names = [];

        foreach ($this->getMigrationFilesData() as $data) {
            $names[] = $data['name'];
        }

        return $names;

    }

    /**
     * @inheritDoc
     */
    public function getFilenameMigration(string $from): string
    {

        return Str::trimToSymbol($from, '/', false);

    }

    /**
     * @inheritDoc
     */
    public function getFullNameMigration(string $from): string
    {

        return Str::trimAfterSymbol($this->getFilenameMigration($from), '.', false);

    }

    /**
     * @inheritDoc
     */
    public function getNameMigration(string $from): string
    {

        $fullName = $this->getFullNameMigration($from);

        return (string) preg_replace(sprintf('/^%s[0-9]+/', self::MIGRATION_FILE_PREFIX), '', $fullName);

    }

    /**
     * @inheritDoc
     */
    public function getTimeMigration(string $migrationFullName): ?int
    {

        preg_match(sprintf('/^%s(?<time>[0-9]+).+$/', self::MIGRATION_FILE_PREFIX), $migrationFullName, $match);

        return $match['time'] ?? null;

    }

    /**
     * @inheritDoc
     */
    public function sortMigrationsByDate(array $migrationFilesData, bool $toLess = false): array
    {

        uasort($migrationFilesData, function (array $oneData, array $twoData) use ($toLess) {
            $timeOne = $this->getTimeMigration($oneData['full-name']);
            $timeTwo = $this->getTimeMigration($twoData['full-name']);

            if($toLess) {
                return $timeTwo > $timeOne;
            }

            return $timeTwo < $timeOne;
        });

        return $migrationFilesData;

    }

    /**
     * @inheritDoc
     */
    public function generateMigrationFullName(string $migrationName): string
    {

        return sprintf('%s%s%s', self::MIGRATION_FILE_PREFIX, time(), ucfirst($migrationName));

    }

}