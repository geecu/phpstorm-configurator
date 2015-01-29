<?php

namespace Gk\PHPStormConfigurator\Plugin;

use Gk\PHPStormConfigurator\ProjectConfigurator;

abstract class AbstractXMLPlugin
    implements PluginInterface
{
    /**
     * @var ProjectConfigurator
     */
    protected $configurator;

    /**
     * @var \DOMDocument
     */
    protected $dom;

    public function __construct(ProjectConfigurator $configurator)
    {
        $this->configurator = $configurator;
        $this->loadXML();
    }

    abstract protected function getXMLTemplate();

    abstract protected function getConfigFileName();

    public function buildConfig()
    {
    }

    public function writeConfig()
    {
        return file_put_contents($this->getConfigFilePath(), $this->dom->saveXML());
    }

    /**
     * @param $configFileName
     * @return string
     */
    protected function getConfigFilePath()
    {
        return $this->configurator->getIdeaPath().DIRECTORY_SEPARATOR.$this->getConfigFileName();
    }

    public function reset()
    {
        $configFilePath = file_exists($this->getConfigFilePath());
        if ($configFilePath) {
            unlink($configFilePath);
        }

        $this->loadXML();
    }

    protected function loadXML()
    {
        $configFilePath = $this->getConfigFilePath();

        if (file_exists($configFilePath)) {
            $contents = file_get_contents($configFilePath);
        } else {
            $contents = $this->getXMLTemplate();
        }

        $this->dom = new \DOMDocument('1.0');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
        $this->dom->loadXML($contents);

        return $this->dom;
    }

    protected function iterateChildren(\DOMElement $el, $callback)
    {
        for ($i = 0; $i < $el->childNodes->length; $i++) {
            $childNode = $el->childNodes->item($i);
            if (!$childNode instanceof \DOMElement) {
                continue;
            }

            $callback($childNode);
        }
    }

    protected function filterChildren(\DOMElement $el, $callback)
    {
        $filtered = [];
        $this->iterateChildren($el, function ($childEl) use ($callback, &$filtered) {
            if ($callback($childEl)) {
                $filtered[] = $childEl;
            }
        });

        return $filtered;
    }

    protected function getElementChild(\DOMElement $el, \DOMElement $checkedEl)
    {
        $existingCallback = function (\DOMElement $childEl) use ($checkedEl) {
            if ($childEl->nodeName !== $checkedEl->nodeName) {
                return false;
            }

            if ($childEl->attributes->length !== $checkedEl->attributes->length) {
                return false;
            }

            foreach ($childEl->attributes as $attribute) {
                if (!$checkedEl->hasAttribute($attribute->nodeName)) {
                    return false;
                }

                if ($checkedEl->getAttribute($attribute->nodeName) != $attribute->nodeValue) {
                    return false;
                }
            }

            return true;
        };

        $filtered = $this->filterChildren($el, $existingCallback);

        if (!count($filtered)) {
            return;
        }

        return reset($filtered);
    }

    protected function ensureChild(\DOMElement $el, $name, $attributes = [])
    {
        $child = $this->dom->createElement($name);
        foreach ($attributes as $attributeName => $attributeValue) {
            $child->setAttribute($attributeName, $attributeValue);
        }

        $existing = $this->getElementChild($el, $child);
        if ($existing !== null) {
            return $existing;
        }

        $el->appendChild($child);

        return $child;
    }
}
