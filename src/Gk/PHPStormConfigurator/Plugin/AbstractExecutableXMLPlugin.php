<?php
/**
 * User: dragulceo
 * Date: 08/02/15
 * Time: 22:08
 */

namespace Gk\PHPStormConfigurator\Plugin;


use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractExecutableXMLPlugin extends AbstractXMLPlugin implements ExecutableInterface
{
    public function execute(InputInterface $input)
    {
        $this->buildConfig();
        $this->writeConfig();
    }

}