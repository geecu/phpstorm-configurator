# phpstorm-configurator
A tool to help configuring phpstorm projects (add excluded folders, enable symfony2 plugin, etc.)

## Installation (globally, using composer)

```
$ composer global require gk/phpstorm-configurator:dev-master
```

make sure you have ``~/.composer/vendor/bin`` in your ``PATH``


```
export PATH="$PATH:$HOME/.composer/vendor/bin"
```

## CLI Usage

```
phpstorm-configurator configure
```

Configures the currently working directory as a PHPStorm project. (The simple usage is useless, you'd better use `pstorm .`)

### Exclude folders

```
phpstorm-configurator configure --exclude app/cache -exclude app/logs
```
or, using the shorthand options
```
phpstorm-configurator configure -x app/cache -x app/logs
```

### Symfony2 plugin

```
phpstorm-configurator configure --plugin symfony2
```
or, using the shorthand options
```
phpstorm-configurator configure -p symfony2
```

This marks ``app/cache`` and ``app/logs`` as excluded and enables the [Symfony2 plugin](http://symfony2-plugin.espend.de/)

## Code usage

If you want to finetune the configuration of the project:

```php
#!/usr/bin/env php
<?php

require_once $_SERVER['HOME'] . "/.composer/vendor/autoload.php";

$projectDir = getcwd();
$configurator = new \Gk\PHPStormConfigurator\ProjectConfigurator($projectDir);

/**
 * Exclude some folders
 */
$imlPlugin = $configurator->getPlugin('iml');
$imlPlugin
    ->addExcludeFolder('app/cache')
    ->addExcludeFolder('app/logs')
;

/**
 * Configure the Symfony2 plugin (http://symfony2-plugin.espend.de/).
 * This also excludes the app/cache and app/logs directories
 */
$symfony2Plugin = $configurator->getPlugin('symfony2');
$symfony2Plugin
    ->addOption("directoryToApp", "app")
    ->addOption("pathToUrlGenerator", "app/cache/dev/adminDevUrlGenerator.php")
    ->addOption("pathToTranslation", "app/cache/dev/translations")
    ->addContainerFile("app/cache/dev/adminDevDebugProjectContainer.xml")
;

$configurator->writeConfig();
```
