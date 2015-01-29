<?php


namespace Gk\PHPStormConfigurator\Plugin;

use Gk\PHPStormConfigurator\ProjectConfigurator;

interface PluginInterface
{
    public function __construct(ProjectConfigurator $configurator);

    public function buildConfig();

    public function writeConfig();

    public function getName();

    public function reset();
}
