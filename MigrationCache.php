<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Caching\Cache;
use Codememory\Components\Caching\Interfaces\CacheInterface;
use Codememory\Components\Markup\Types\YamlType;
use Codememory\FileSystem\File;

/**
 * Class MigrationCache
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
class MigrationCache
{

    private const TYPE_CACHE = '__cdm-migrations';
    private const NAME_CACHE = 'migration-%s';


    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * MigrationCache Construct
     */
    public function __construct()
    {

        $this->cache = new Cache(new YamlType(), new File());

    }

    /**
     * @param MigrationSchema $migrationSchema
     * @param string          $migrationName
     *
     * @return void
     */
    public function createCache(MigrationSchema $migrationSchema, string $migrationName): void
    {

        $cacheName = $this->generateCacheName($migrationName);

        $this->cache->create(self::TYPE_CACHE, $cacheName, $migrationSchema->getQueries());

    }

    /**
     * @param string $migrationName
     *
     * @return array
     */
    public function getCache(string $migrationName): array
    {

        $cacheName = $this->generateCacheName($migrationName);

        return $this->cache->get(self::TYPE_CACHE, $cacheName);

    }

    /**
     * @param string $migrationName
     *
     * @return string
     */
    public function generateCacheName(string $migrationName): string
    {

        return sprintf(self::NAME_CACHE, $migrationName);

    }

}