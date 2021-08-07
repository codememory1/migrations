<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Database\Connection\Interfaces\ConnectorInterface;
use Codememory\Components\Database\QueryBuilder\Exceptions\NotSelectedStatementException;
use Codememory\Components\Database\QueryBuilder\Interfaces\QueryBuilderInterface;
use Codememory\Components\Database\QueryBuilder\QueryBuilder;
use Codememory\Components\Database\Schema\StatementComponents\Column;
use Codememory\Components\Database\Schema\Statements\Definition\CreateTable;

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

    /**
     * @var ConnectorInterface
     */
    private ConnectorInterface $connector;

    /**
     * @var QueryBuilderInterface
     */
    private QueryBuilderInterface $queryBuilder;

    /**
     * @param ConnectorInterface $connector
     */
    public function __construct(ConnectorInterface $connector)
    {

        $this->connector = $connector;
        $this->queryBuilder = new QueryBuilder($this->connector);

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
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get all records from migration table
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     * @throws NotSelectedStatementException
     */
    public function getRecords(): array
    {

        $qb = clone $this->queryBuilder;

        $qb->select()->from(self::MIGRATION_TABLE_NAME);

        return $qb->generateQuery()->getResult()->toArray();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get an entry from the migrations table by migration name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return array
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
            );

        return $qb
            ->setParameter('name', $name)
            ->generateQuery()
            ->getResult()
            ->toArray();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add entries to migration table
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $records
     *
     * @return void
     * @throws NotSelectedStatementException
     */
    public function addRecords(array $records): void
    {

        $qb = clone $this->queryBuilder;
        $columns = array_keys($records[array_key_last($records)]);

        $qb
            ->insert(self::MIGRATION_TABLE_NAME)
            ->columns(...$columns)
            ->records(...$records);

        $qb->generateQuery()->execute();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check the existence of a record by name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $migrationName
     *
     * @return bool
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
     */
    public function updateRecord(string $migrationName, array $data): void
    {

        $qb = clone $this->queryBuilder;

        $qb
            ->update([self::MIGRATION_TABLE_NAME])
            ->setData(array_keys($data), $data)
            ->where(
                $qb->expression()->exprAnd(
                    $qb->expression()->condition('name', '=', ':name')
                )
            );

        $qb->setParameter('name', $migrationName)->generateQuery()->execute();

    }

    /**
     * @param string $migrationName
     *
     * @return void
     */
    public function deleteRecord(string $migrationName): void
    {

        $qb = clone $this->queryBuilder;

        $qb
            ->delete(self::MIGRATION_TABLE_NAME)
            ->where(
                $qb->expression()->exprAnd(
                    $qb->expression()->condition('name', '=', ':name')
                )
            );

        $qb->setParameter('name', $migrationName)->generateQuery()->execute();

    }

}