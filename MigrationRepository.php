<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Database\Connection\Interfaces\ConnectorInterface;
use Codememory\Components\Database\Orm\QueryBuilder\Answer\Result;
use Codememory\Components\Database\QueryBuilder\Exceptions\StatementNotSelectedException;
use Codememory\Components\Database\QueryBuilder\QueryBuilder;
use Codememory\Components\Database\Schema\StatementComponents\Column;
use Codememory\Components\Database\Schema\Statements\Definition\CreateTable;
use JetBrains\PhpStorm\Pure;
use PDO;

/**
 * Class MigrationRepository
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
class MigrationRepository
{

    public const MIGRATION_TABLE_NAME = 'codememory_migrations';
    public const QUERY_BUILDER_CREATOR = '__cdm-migrations';

    /**
     * @var ConnectorInterface
     */
    private ConnectorInterface $connector;

    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    /**
     * @param ConnectorInterface $connector
     */
    #[Pure]
    public function __construct(ConnectorInterface $connector)
    {

        $this->connector = $connector;
        $this->queryBuilder = new QueryBuilder($this->connector, self::QUERY_BUILDER_CREATOR);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns SQL create migration table
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function getQueryTable(): string
    {

        $tableCreator = new CreateTable();
        $column = new Column();

        $column
            ->setColumnName('name')
            ->varchar(200)
            ->notNull()
            ->primary();
        $column
            ->setColumnName('executed_at')
            ->datetime()
            ->notNull();
        $column
            ->setColumnName('execution_time')
            ->int()
            ->notNull();

        $tableCreator
            ->table(self::MIGRATION_TABLE_NAME)
            ->columns($column)
            ->engine()
            ->collate('utf8_general_ci');

        return $tableCreator->getQuery();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Creates migration tables
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    public function createTable(): void
    {

        $this->connector->getConnection()->exec($this->getQueryTable());

    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return Result
     * @throws StatementNotSelectedException
     */
    public function generateResult(QueryBuilder $queryBuilder): Result
    {

        $records = $queryBuilder->getExecutor()->execute(
            $queryBuilder->getStatement()->getQuery(),
            $queryBuilder->getParameters()
        )->fetchAll(PDO::FETCH_ASSOC);

        return new Result($records);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get all records from migration table
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     * @throws StatementNotSelectedException
     */
    public function getRecords(): array
    {

        $qb = clone $this->queryBuilder;

        $qb->select()->from(self::MIGRATION_TABLE_NAME)->execute();

        return $this->generateResult($qb)->all();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get an entry from the migrations table by migration name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return array
     * @throws StatementNotSelectedException
     */
    public function getRecordByName(string $name): array
    {

        $qb = clone $this->queryBuilder;

        $qb
            ->select()
            ->from(self::MIGRATION_TABLE_NAME)
            ->where(
                $qb->expression()->exprAnd(
                    $qb->expression()->condition('name', '=', ':name')
                )
            )
            ->setParameter('name', $name);

        return $this->generateResult($qb)->all();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add entries to migration table
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $records
     *
     * @return void
     * @throws StatementNotSelectedException
     */
    public function addRecords(array $records): void
    {

        $qb = clone $this->queryBuilder;
        $columns = array_keys($records[array_key_last($records)]);

        $qb
            ->insert(self::MIGRATION_TABLE_NAME)
            ->setRecords($columns, ...$records)
            ->execute();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check the existence of a record by name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $migrationName
     *
     * @return bool
     * @throws StatementNotSelectedException
     */
    public function existRecord(string $migrationName): bool
    {

        return [] !== $this->getRecordByName($migrationName);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Update record by migration name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $migrationName
     * @param array  $data
     *
     * @return void
     * @throws StatementNotSelectedException
     */
    public function updateRecord(string $migrationName, array $data): void
    {

        $qb = clone $this->queryBuilder;

        $qb
            ->update(self::MIGRATION_TABLE_NAME)
            ->updateData(array_keys($data), $data)
            ->where(
                $qb->expression()->exprAnd(
                    $qb->expression()->condition('name', '=', ':name')
                )
            )
            ->setParameter('name', $migrationName)
            ->execute();

    }

    /**
     * @param string $migrationName
     *
     * @return void
     * @throws StatementNotSelectedException
     */
    public function deleteRecord(string $migrationName): void
    {

        $qb = clone $this->queryBuilder;

        $qb
            ->delete()
            ->from(self::MIGRATION_TABLE_NAME)
            ->where(
                $qb->expression()->exprAnd(
                    $qb->expression()->condition('name', '=', ':name')
                )
            )
            ->setParameter('name', $migrationName)
            ->execute();

    }

}