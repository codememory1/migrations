<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Database\Connection\Interfaces\ConnectorInterface;
use Codememory\Components\Database\QueryBuilder\Exceptions\StatementNotSelectedException;
use Codememory\Components\DateTime\DateTime;
use Codememory\Components\DateTime\Exceptions\InvalidTimezoneException;

/**
 * Class Executor
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
class Executor
{

    /**
     * @var ConnectorInterface
     */
    private ConnectorInterface $connector;

    /**
     * @var MigrationRepository
     */
    private MigrationRepository $migrationRepository;

    /**
     * @var MigrationCache
     */
    private MigrationCache $migrationCache;

    /**
     * @param ConnectorInterface $connector
     */
    public function __construct(ConnectorInterface $connector)
    {

        $this->connector = $connector;
        $this->migrationRepository = new MigrationRepository($this->connector);
        $this->migrationCache = new MigrationCache();

    }

    /**
     * @param array           $migrationData
     * @param MigrationSchema $schema
     *
     * @return array|bool
     * @throws InvalidTimezoneException
     * @throws StatementNotSelectedException
     */
    public function exec(array $migrationData, MigrationSchema $schema): array|bool
    {

        if (!$this->migrationRepository->existRecord($migrationData['full-name'])) {
            $microTime = microtime(true);

            if ([] !== $schema->getQueries()) {
                $this->migrationCache->createCache($schema, $migrationData['name']);
                $this->connector->getConnection()->exec(implode(';', $schema->getQueries()));
            }

            $ms = (microtime(true) - $microTime) * 1000;

            $this->migrationRepository->addRecords([
                [
                    'name'           => $migrationData['full-name'],
                    'executed_at'    => (new DateTime())->now(),
                    'execution_time' => round($ms)
                ]
            ]);

            return [
                'count-queries'  => count($schema->getQueries()),
                'execution-time' => round($ms)
            ];

        }

        return false;

    }

}