<?php

namespace Gk\PHPStormConfigurator\Plugin;


class ImlPlugin extends AbstractXMLPlugin
{

    protected function getXMLTemplate()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<module type="WEB_MODULE" version="4">
  <component name="NewModuleRootManager">
    <content url="file://\$MODULE_DIR\$"></content>
    <orderEntry type="inheritedJdk" />
    <orderEntry type="sourceFolder" forTests="false" />
</component>
</module>
XML;
    }

    protected function getConfigFileName()
    {
        return sprintf('%s.iml', $this->configurator->getProjectName());
    }

    public function getName()
    {
        return 'iml';
    }

    public function addExcludeFolder($path)
    {
        $url = $this->pathToURL($path);

        $this->ensureChild($this->getContentNode(), 'excludeFolder', ['url' => $url]);

        return $this;
    }

    private function getContentNode()
    {
        return $this->dom
            ->childNodes->item(0)//module
            ->childNodes->item(0)//component
            ->childNodes->item(0)//content
            ;
    }

    private function pathToURL($path)
    {
        $relativePath = preg_replace('@^'.$this->configurator->getPath().'@i', '', $path);
        $relativePath = ltrim($relativePath, '/');

        return sprintf('file://$MODULE_DIR$/%s', $relativePath);
    }
}
