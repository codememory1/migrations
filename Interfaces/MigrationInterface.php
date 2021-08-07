<?php

namespace Codememory\Components\Database\Migrations\Interfaces;

use Codememory\Components\Database\Migrations\Interfaces\SchemaInterface as MigrationSchemaInterface;

/**
 * Interface MigrationInterface
 *
 * @package Codememory\Components\Database\Migrations\Interfaces
 *
 * @author  Codememory
 */
interface MigrationInterface
{

    /**
     * @param MigrationSchemaInterface $schema
     *
     * @return void
     */
    public function up(MigrationSchemaInterface $schema): void;

    /**
     * @param MigrationSchemaInterface $schema
     *
     * @return void
     */
    public function down(MigrationSchemaInterface $schema): void;

}