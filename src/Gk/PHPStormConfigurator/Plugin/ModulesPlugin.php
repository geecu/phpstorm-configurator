<?php

namespace Gk\PHPStormConfigurator\Plugin;


class ModulesPlugin extends AbstractXMLPlugin
{

    protected function getXMLTemplate()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
  <component name="ProjectModuleManager">
    <modules />
  </component>
</project>
XML;
    }

    protected function getConfigFileName()
    {
        return 'modules.xml';
    }

    public function getName()
    {
        return 'modules';
    }

    public function buildConfig()
    {
        $path = sprintf('$PROJECT_DIR$/.idea/%s', $this->configurator->getPlugin('iml')->getConfigFileName());
        $url = sprintf('file://%s', $path);

        $this->ensureChild($this->getModulesNode(), 'module', array(
            'fileurl' => $url,
            'filepath' => $path,
        ));

        return $this;
    }

    private function getModulesNode()
    {
        return $this->dom
            ->childNodes->item(0)//project
            ->childNodes->item(0)//component
            ->childNodes->item(0)//modules
            ;
    }
}
