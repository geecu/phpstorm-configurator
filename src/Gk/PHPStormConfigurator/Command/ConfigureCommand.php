<?php

namespace Gk\PHPStormConfigurator\Command;

use Gk\PHPStormConfigurator\ProjectConfigurator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ConfigureCommand extends Command
{
    protected function configure()
    {
        $this->setName('configure')
            ->setDescription('Initializes a PHPStorm project, optionally allowing to add some extra configurations (excluded/favorite folders/enable symfony2 plugin, etc.)')
            ->addOption(
                'plugin',
                'p',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Enable plugin',
                null
            )
            ->addOption(
                'exclude',
                'x',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Exclude folder',
                null
            )
            ->addArgument('path',
                InputArgument::OPTIONAL,
                'The path to the project (defaults to current working directory)',
                getcwd()
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configurator = new ProjectConfigurator($input->getArgument('path'));

        foreach ($input->getOption('exclude') as $excludedFolder) {
            $configurator->getPlugin('iml')
                ->addExcludeFolder($excludedFolder);
        }

        foreach ($input->getOption('plugin') as $pluginName) {
            $configurator->enablePlugin($pluginName);
        }

        $configurator->writeConfig();
    }
}
