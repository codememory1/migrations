<?php

namespace Codememory\Components\Database\Migrations\Interfaces;

/**
 * Interface MigrationDataInterface
 *
 * @package Codememory\Components\Database\Migrations\Interfaces
 *
 * @author  Codememory
 */
interface MigrationDataInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get complete information about migration files
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getMigrationFilesData(): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a dataset of a migration file, by the name of the migration
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $migrationName
     *
     * @return array
     */
    public function getMigrationFileDataByName(string $migrationName): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get key value from dataset of migration files by migration name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $migrationName
     * @param string $key
     *
     * @return mixed
     */
    public function getKeyValueByName(string $migrationName, string $key): mixed;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of migration files with a project-relative path
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getMigrationFiles(): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of migration names
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getMigrationNames(): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the name of the migrations file from variable $from
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $from
     *
     * @return string
     */
    public function getFilenameMigration(string $from): string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the name of the migrations file from variable $from
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $from
     *
     * @return string
     */
    public function getFullNameMigration(string $from): string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the full migration name from the $from variable
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $from
     *
     * @return string
     */
    public function getNameMigration(string $from): string;

    /**
     * @param string $migrationFullName
     *
     * @return int|null
     */
    public function getTimeMigration(string $migrationFullName): ?int;

    /**
     * @param array $migrationFilesData
     *
     * @return array
     */
    public function sortMigrationsByDate(array $migrationFilesData): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Generate full migration name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $migrationName
     *
     * @return string
     */
    public function generateMigrationFullName(string $migrationName): string;

}