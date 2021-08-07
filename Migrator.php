<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Database\Migrations\Interfaces\MigrationInterface;

/**
 * Class Migrator
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
class Migrator
{

    /**
     * @var MigrationInterface
     */
    private MigrationInterface $migration;

    /**
     * @param MigrationInterface $migration
     */
    public function __construct(MigrationInterface $migration)
    {

        $this->migration = $migration;

    }

    /**
     * @return MigrationSchema
     */
    final public function getUp(): MigrationSchema
    {

        $schema = new MigrationSchema();

        $this->migration->up($schema);

        return $schema;

    }

    /**
     * @return MigrationSchema
     */
    final public function getDown(): MigrationSchema
    {

        $schema = new MigrationSchema();

        $this->migration->down($schema);

        return $schema;

    }

}