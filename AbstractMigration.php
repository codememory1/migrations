<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Database\Migrations\Interfaces\MigrationInterface;
use Codememory\Components\Database\Migrations\Interfaces\SchemaInterface as MigrationSchemaInterface;
use RuntimeException;

/**
 * Class AbstractMigration
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
abstract class AbstractMigration implements MigrationInterface
{

    /**
     * @inheritDoc
     */
    public function up(MigrationSchemaInterface $schema): void
    {

        $this->methodOverrideException('up');

    }

    /**
     * @inheritDoc
     */
    public function down(MigrationSchemaInterface $schema): void
    {

        $this->methodOverrideException('down');

    }

    /**
     * @param string $method
     *
     * @return void
     */
    private function methodOverrideException(string $method): void
    {

        throw new RuntimeException(sprintf('Method "%s" not overridden in "%s" migration', $method, static::class));

    }

}