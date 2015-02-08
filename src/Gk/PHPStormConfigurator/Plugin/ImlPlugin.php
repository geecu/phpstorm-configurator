<?php

namespace Gk\PHPStormConfigurator\Plugin;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class ImlPlugin extends AbstractXMLPlugin implements CommandConfiguratorInterface, ExecutableInterface
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
        $relativePath = preg_replace('@^' . $this->configurator->getPath() . '@i', '', $path);
        $relativePath = ltrim($relativePath, '/');

        return sprintf('file://$MODULE_DIR$/%s', $relativePath);
    }

    public function configureCommand(Command $command)
    {
        $command->addOption(
            'exclude',
            'x',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Exclude folder',
            null
        );
    }

    public function execute(InputInterface $input)
    {
        foreach ($input->getOption('exclude') as $excludedFolder) {
            $this->addExcludeFolder($excludedFolder);
        }

    }
}
