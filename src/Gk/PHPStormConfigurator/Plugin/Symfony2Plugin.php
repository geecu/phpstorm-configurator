<?php

namespace Gk\PHPStormConfigurator\Plugin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class Symfony2Plugin extends AbstractXMLPlugin implements ExecutableInterface, CommandConfiguratorInterface
{

    protected function getXMLTemplate()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
  <component name="Symfony2PluginSettings">
    <option name="pluginEnabled" value="true" />
  </component>
</project>
XML;
    }

    protected function getConfigFileName()
    {
        return 'symfony2.xml';
    }

    public function getName()
    {
        return 'symfony2';
    }

    public function buildConfig()
    {
        $iml = $this->configurator->getPlugin('iml');

        $iml->addExcludeFolder('app/cache');
        $iml->addExcludeFolder('app/logs');
    }

    public function addOption($name, $value)
    {
        $this->ensureChild($this->getComponentNode(), 'option', ['name' => $name, 'value' => $value]);

        return $this;
    }

    public function addContainerFile($path)
    {
        $containerFiles = $this->ensureChild($this->getComponentNode(), 'option', ['name' => 'containerFiles']);
        $list = $this->ensureChild($containerFiles, 'list');
        $this->ensureChild($list, 'container_file', ['path' => $path]);

        return $this;
    }

    public function getComponentNode()
    {
        return $this->dom
            ->childNodes->item(0)//project
            ->childNodes->item(0)//component
            ;
    }

    public function execute(InputInterface $input)
    {
        $this->buildConfig();
        $this->writeConfig();

        $iml = $this->configurator->getPlugin('iml');
        $iml->writeConfig();
    }

    public function configureCommand(Command $command)
    {
        $command->addOption(
            'plugin-symfony2',
            's',
            InputOption::VALUE_NONE,
            'Enable symfony2 plugin',
            null
        );
    }
}
