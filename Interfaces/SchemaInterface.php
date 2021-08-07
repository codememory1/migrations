<?php

namespace Codememory\Components\Database\Migrations\Interfaces;

use Codememory\Components\Database\Migrations\MigrationSchema;
use Codememory\Components\Database\Schema\Interfaces\DeleteInterface;
use Codememory\Components\Database\Schema\Interfaces\ExpressionInterface;
use Codememory\Components\Database\Schema\Interfaces\UpdateInterface;
use Codememory\Components\Database\Schema\Statements\Definition\AddToTable;
use Codememory\Components\Database\Schema\Statements\Definition\ChangeToTable;
use Codememory\Components\Database\Schema\Statements\Definition\CreateTable;
use Codememory\Components\Database\Schema\Statements\Definition\DropToTable;
use Codememory\Components\Database\Schema\Statements\Definition\Rename;
use JetBrains\PhpStorm\Pure;

/**
 * Interface SchemaInterface
 *
 * @package Codememory\Components\Database\Migrations\Interfaces
 *
 * @author  Codememory
 */
interface SchemaInterface
{

    /**
     * @param string $sql
     *
     * @return void
     */
    public function addSql(string $sql): void;

    /**
     * @param string $tableName
     *
     * @return void
     */
    public function selectTable(string $tableName): void;

    /**
     * @param callable $callback
     *
     * @return CreateTable
     */
    public function createTable(callable $callback): CreateTable;

    /**
     * @return DropToTable
     */
    public function dropToTable(): DropToTable;

    /**
     * @return void
     */
    public function dropTable(): void;

    /**
     * @return AddToTable
     */
    public function addToTable(): AddToTable;

    /**
     * @param callable $callback
     *
     * @return SchemaInterface
     */
    public function addColumn(callable $callback): SchemaInterface;

    /**
     * @param callable $callback
     *
     * @return SchemaInterface
     */
    public function addColumns(callable $callback): SchemaInterface;

    /**
     * @param string $columnName
     *
     * @return SchemaInterface
     */
    public function dropColumn(string $columnName): SchemaInterface;

    /**
     * @param array $columns
     *
     * @return SchemaInterface
     */
    public function dropColumns(array $columns): SchemaInterface;

    /**
     * @return void
     */
    public function dropPrimary(): void;

    /**
     * @param string $columnName
     */
    public function dropForeign(string $columnName): void;

    /**
     * @return ChangeToTable
     */
    public function changeToTable(): ChangeToTable;

    /**
     * @param string   $columnName
     * @param callable $callback
     *
     * @return void
     */
    public function modifyColumn(string $columnName, callable $callback): void;

    /**
     * @return Rename
     */
    public function renameToTable(): Rename;

    /**
     * @param string $newTableName
     *
     * @return void
     */
    public function renameTable(string $newTableName): void;

    /**
     * @param string $oldColumnName
     * @param string $newColumnName
     *
     * @return void
     */
    public function renameColumn(string $oldColumnName, string $newColumnName): void;

    /**
     * @return ExpressionInterface
     */
    public function expression(): ExpressionInterface;

    /**
     * @param array $columns
     * @param mixed ...$records
     *
     * @return SchemaInterface
     */
    public function insertRecords(array $columns, array ...$records): SchemaInterface;

    /**
     * @param array $data
     *
     * @return UpdateInterface
     */
    public function updateRecords(array $data): UpdateInterface;

    /**
     * @return DeleteInterface
     */
    public function deleteRecords(): DeleteInterface;

}