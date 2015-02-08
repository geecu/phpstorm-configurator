<?php

namespace Gk\PHPStormConfigurator;

use Gk\PHPStormConfigurator\Plugin\PluginInterface;
use Symfony\Component\Finder\Finder;

class ProjectConfigurator
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $projectName;
    /**
     * @var string
     */
    private $ideaPath;
    /**
     * @var PluginInterface[]
     */
    private $plugins = [];
    /**
     * @var PluginInterface[]
     */
    private $availablePlugins = [];

    public function __construct($path = null, $projectName = '')
    {
        if($path) {
            $this->setPath($path);
        }

        $this->projectName = $projectName;

        $this->registerBuiltinPlugins();

        $this->enablePlugin('iml');
        $this->enablePlugin('modules');
    }

    public function setPath($path)
    {
        $this->path = $path;
        $this->ideaPath = $this->path . DIRECTORY_SEPARATOR . '.idea';

        if (!file_exists($this->ideaPath)) {
            mkdir($this->ideaPath);
        }

        if (!is_dir($this->ideaPath)) {
            throw new \RuntimeException(sprintf('Path to project (%s) is not a directory', $this->ideaPath));
        }

        if (empty($this->projectName)) {
            $this->projectName = basename($this->getPath());
        }
    }

    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getIdeaPath()
    {
        return $this->ideaPath;
    }

    /**
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    public function writeConfig()
    {
        foreach ($this->plugins as $plugin) {
            $plugin->buildConfig();
        }

        foreach ($this->plugins as $plugin) {
            $plugin->writeConfig();
        }
    }

    public function getPlugin($name)
    {
        if (isset($this->plugins[$name])) {
            return $this->plugins[$name];
        }

        return $this->enablePlugin($name);
    }

    public function enablePlugin($name)
    {
        if (isset($this->availablePlugins[$name])) {
            return $this->plugins[$name] = $this->availablePlugins[$name];
        }

        throw new \RuntimeException(sprintf('Plugin %s is not registered. Available plugins are: %s',
            $name,
            implode(', ', array_keys($this->availablePlugins)
            )));
    }

    public function registerPlugin($name, $plugin)
    {
        $this->availablePlugins[$name] = $plugin;
    }

    protected function registerBuiltinPlugins()
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . 'Plugin';
        $finder = new Finder();
        $finder->files()->name('*Plugin.php')->in($dir);

        $prefix = __NAMESPACE__ . '\\Plugin';
        foreach ($finder as $pluginFile) {
            $ns = $prefix;
            if ($relativePath = $pluginFile->getRelativePath()) {
                $ns .= '\\' . strtr($relativePath, '/', '\\');
            }
            $class = $ns . '\\' . $pluginFile->getBasename('.php');

            $r = new \ReflectionClass($class);
            if ($r->isSubclassOf($prefix . '\\PluginInterface')
                && !$r->isAbstract()
                && $r->getConstructor()->getNumberOfRequiredParameters() == 1
            ) {
                /** @var PluginInterface $plugin */
                $plugin = $r->newInstance($this);
                $this->registerPlugin($plugin->getName(), $plugin);
            }
        }
    }

    /**
     * @return Plugin\PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @return Plugin\PluginInterface[]
     */
    public function getAvailablePlugins()
    {
        return $this->availablePlugins;
    }


}
