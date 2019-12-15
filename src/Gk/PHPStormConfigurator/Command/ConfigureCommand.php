<?php

namespace Gk\PHPStormConfigurator\Command;

use Gk\PHPStormConfigurator\Plugin\CommandConfiguratorInterface;
use Gk\PHPStormConfigurator\Plugin\ExecutableInterface;
use Gk\PHPStormConfigurator\ProjectConfigurator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ConfigureCommand extends Command
{
    protected $configurator;

    /**
     * @return ProjectConfigurator
     */
    protected function getConfigurator()
    {
        if (!$this->configurator) {
            $this->configurator = new ProjectConfigurator();
        }
        return $this->configurator;
    }


    protected function configure()
    {
        $this->setName('configure')
            ->setDescription('Initializes a PHPStorm project, optionally allowing to add some extra configurations (excluded/favorite folders/enable symfony2 plugin, etc.)')
            ->addArgument('path',
                InputArgument::OPTIONAL,
                'The path to the project (defaults to current working directory)',
                getcwd()
            )
        ;
        $plugins = $this->getConfigurator()->getPlugins();
        foreach ($plugins as $plugin) {
            if ($plugin instanceof CommandConfiguratorInterface) {
                $plugin->configureCommand($this);
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configurator = $this->getConfigurator();
        $configurator->setPath($input->getArgument('path'));

        $plugins = $this->getConfigurator()->getPlugins();
        foreach ($plugins as $plugin) {
            if ($plugin instanceof ExecutableInterface) {
                $plugin->execute($input);
            }
        }
    }
}
