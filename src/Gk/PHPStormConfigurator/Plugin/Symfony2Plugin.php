<?php

namespace Gk\PHPStormConfigurator\Plugin;

class Symfony2Plugin extends AbstractXMLPlugin
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
}
