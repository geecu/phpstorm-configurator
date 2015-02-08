<?php
/**
 * User: dragulceo
 * Date: 30/01/15
 * Time: 23:12
 */

namespace Gk\PHPStormConfigurator\Plugin;


use Symfony\Component\Console\Input\InputInterface;

interface ExecutableInterface
{
    public function execute(InputInterface $input);
}