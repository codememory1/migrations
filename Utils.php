<?php

namespace Codememory\Components\Database\Migrations;

use Codememory\Components\Caching\Exceptions\ConfigPathNotExistException;
use Codememory\Components\Configuration\Config;
use Codememory\Components\Configuration\Configuration;
use Codememory\Components\Configuration\Exceptions\ConfigNotFoundException;
use Codememory\Components\Configuration\Interfaces\ConfigInterface;
use Codememory\Components\Environment\Exceptions\EnvironmentVariableNotFoundException;
use Codememory\Components\Environment\Exceptions\IncorrectPathToEnviException;
use Codememory\Components\Environment\Exceptions\ParsingErrorException;
use Codememory\Components\Environment\Exceptions\VariableParsingErrorException;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\FileSystem\File;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Utils
 *
 * @package Codememory\Components\Database\Migrations
 *
 * @author  Codememory
 */
class Utils
{

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Utils Construct.
     */
    public function __construct()
    {

        $this->config = Configuration::getInstance()->open(GlobalConfig::get('migrations.configName'), $this->defaultConfig());

    }

    /**
     * @return string
     */
    public function getPathWithMigrations(): string
    {

        return trim($this->config->get('migrations.pathWithMigrations'), '/').'/';

    }

    /**
     * @return string
     */
    public function getNamespaceMigration(): string
    {

        return trim($this->config->get('migrations.namespaceMigration'), '\\').'\\';

    }

    /**
     * @return array
     */
    #[ArrayShape(['pathWithMigrations' => "string", 'namespaceMigration' => "string"])]
    private function defaultConfig(): array
    {

        return [
            'pathWithMigrations' => GlobalConfig::get('migrations.pathWithMigrations'),
            'namespaceMigration' => GlobalConfig::get('migrations.namespaceMigration')
        ];

    }

}