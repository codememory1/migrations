<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Database\Migrations\Interfaces\SchemaInterface;
use Codememory\Components\Database\Schema\Interfaces\DeleteInterface;
use Codememory\Components\Database\Schema\Interfaces\ExpressionInterface;
use Codememory\Components\Database\Schema\Interfaces\UpdateInterface;
use Codememory\Components\Database\Schema\StatementComponents\Column;
use Codememory\Components\Database\Schema\StatementComponents\Expression;
use Codememory\Components\Database\Schema\StatementComponents\Reference;
use Codememory\Components\Database\Schema\Statements\Definition\AddToTable;
use Codememory\Components\Database\Schema\Statements\Definition\ChangeToTable;
use Codememory\Components\Database\Schema\Statements\Definition\CreateTable;
use Codememory\Components\Database\Schema\Statements\Definition\DropTable;
use Codememory\Components\Database\Schema\Statements\Definition\DropToTable;
use Codememory\Components\Database\Schema\Statements\Definition\Rename;
use Codememory\Components\Database\Schema\Statements\Manipulation\Delete;
use Codememory\Components\Database\Schema\Statements\Manipulation\Insert;
use Codememory\Components\Database\Schema\Statements\Manipulation\Update;
use JetBrains\PhpStorm\Pure;

/**
 * Class MigrationSchema
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
class MigrationSchema implements SchemaInterface
{

    /**
     * @var array
     */
    private array $queries = [];

    /**
     * @var string
     */
    private string $table;

    /**
     * @inheritDoc
     */
    public function addSql(string $sql): void
    {

        $this->queries[] = $sql;

    }

    /**
     * @inheritDoc
     */
    public function selectTable(string $tableName): void
    {

        $this->table = $tableName;

    }

    /**
     * @inheritDoc
     */
    public function createTable(callable $callback): CreateTable
    {

        $tableCreator = new CreateTable();
        $column = new Column();
        $reference = new Reference();

        call_user_func($callback, $column, $reference);

        $tableCreator->table($this->table)->columns($column, [] === $reference->getReferences() ? null : $reference);

        $this->queries[] = $tableCreator;

        return $tableCreator;

    }

    /**
     * @inheritDoc
     */
    public function dropToTable(): DropToTable
    {

        $dropToTable = new DropToTable();

        $this->queries[] = $dropToTable->table($this->table);

        return $dropToTable;

    }

    /**
     * @inheritDoc
     */
    public function dropTable(): void
    {

        $dropTable = new DropTable();

        $this->queries[] = $dropTable->table($this->table);

    }

    /**
     * @inheritDoc
     */
    public function addToTable(): AddToTable
    {

        $addToTable = new AddToTable();

        $this->queries[] = $addToTable->table($this->table);

        return $addToTable;

    }

    /**
     * @inheritDoc
     */
    public function addColumn(callable $callback): MigrationSchema
    {

        $column = new Column();

        call_user_func($callback, $column);

        $this->addToTable()->addColumn($column);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function addColumns(callable $callback): MigrationSchema
    {

        $column = new Column();

        call_user_func($callback, $column);

        $this->addToTable()->addMultipleColumn($column);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function dropColumn(string $columnName): MigrationSchema
    {

        $this->dropToTable()->dropColumn($columnName);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function dropColumns(array $columns): MigrationSchema
    {

        foreach ($columns as $column) {
            $this->dropColumn($column);
        }

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function dropPrimary(): void
    {

        $this->addSql(sprintf('ALTER TABLE `%s` DROP PRIMARY KEY', $this->table));

    }

    /**
     * @inheritDoc
     */
    public function dropForeign(string $columnName): void
    {

        $this->dropToTable()->dropForeign(sprintf('%s_fk', $columnName));

    }

    /**
     * @inheritDoc
     */
    public function changeToTable(): ChangeToTable
    {

        $changeToTable = new ChangeToTable();

        $this->queries[] = $changeToTable->table($this->table);

        return $changeToTable;

    }

    /**
     * @inheritDoc
     */
    public function modifyColumn(string $columnName, callable $callback): void
    {

        $changeToTable = $this->changeToTable();
        $column = new Column();

        $column->setColumnName($columnName);

        call_user_func($callback, $column);

        $changeToTable->modifyColumn($column);

    }

    /**
     * @inheritDoc
     */
    public function renameToTable(): Rename
    {

        $rename = new Rename();

        $this->queries[] = $rename->table($this->table);

        return $rename;

    }

    /**
     * @inheritDoc
     */
    public function renameTable(string $newTableName): void
    {

        $this->renameToTable()->renameTable($newTableName);

    }

    /**
     * @inheritDoc
     */
    public function renameColumn(string $oldColumnName, string $newColumnName): void
    {

        $this->renameToTable()->renameColumn($oldColumnName, $newColumnName);

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function expression(): ExpressionInterface
    {

        return new Expression();

    }

    /**
     * @inheritDoc
     */
    public function insertRecords(array $columns, array ...$records): MigrationSchema
    {

        $insert = new Insert();

        $this->queries[] = $insert->insert()
            ->table($this->table)
            ->columns(...$columns)
            ->records(...$records);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function updateRecords(array $data): UpdateInterface
    {

        $update = new Update();

        $this->queries[] = $update->update()
            ->tables([$this->table])
            ->setData(array_keys($data), $data);

        return $update;

    }

    /**
     * @inheritDoc
     */
    public function deleteRecords(): DeleteInterface
    {

        $delete = new Delete();

        $this->queries[] = $delete->delete()
            ->from([$this->table]);

        return $delete;

    }

    /**
     * @return array
     */
    public function getQueries(): array
    {

        $queries = [];

        foreach ($this->queries as $query) {
            if (is_string($query)) {
                $queries[] = $query;
            } else {
                $queries[] = $query->getQuery();
            }
        }

        return $queries;

    }

}