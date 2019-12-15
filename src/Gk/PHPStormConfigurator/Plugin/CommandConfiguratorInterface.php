<?php
/**
 * User: dragulceo
 * Date: 08/02/15
 * Time: 20:53
 */

namespace Gk\PHPStormConfigurator\Plugin;


use Symfony\Component\Console\Command\Command;

interface CommandConfiguratorInterface
{

    public function configureCommand(Command $command);
}