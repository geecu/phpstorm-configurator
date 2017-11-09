<?php

namespace Gk\PHPStormConfigurator\Plugin;


use Gk\PHPStormConfigurator\ProjectConfigurator;

class VcsPlugin extends AbstractXMLPlugin
{

    protected function getXMLTemplate()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
  <component name="VcsDirectoryMappings">
  </component>
</project>
XML;
    }

    protected function getConfigFileName()
    {
        return 'vcs.xml';
    }

    public function getName()
    {
        return 'vcs';
    }

    public function buildConfig()
    {
        $this->addDirectory('');

        return $this;
    }

    public function addDirectory($relativePath, $type = 'Git')
    {
        $this->ensureChild($this->getComponentNode(), 'mapping', [
            'directory' => sprintf('$PROJECT_DIR$%s', $relativePath),
            'vcs' => $type
        ]);

        return $this;
    }

    private function getComponentNode()
    {
        return $this->dom
            ->childNodes->item(0)//project
            ->childNodes->item(0)//component
            ;
    }
}
