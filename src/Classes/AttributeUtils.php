<?php

namespace Akyos\Core\Classes;

class AttributeUtils
{

    public $pathTheme = DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Attributes'.DIRECTORY_SEPARATOR;
    public $pathCore = DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'akyos'.DIRECTORY_SEPARATOR.'akyos-x-core'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Attributes'.DIRECTORY_SEPARATOR;

    public function __construct()
    {
        $this->pathTheme = get_template_directory().$this->pathTheme;
        $this->pathCore = get_template_directory().$this->pathCore;
    }

    public function getAttributesAkyosCore(): \RecursiveIteratorIterator
    {
        $directory = new \RecursiveDirectoryIterator($this->pathCore);
        $iterator = new \RecursiveIteratorIterator($directory);

        return $iterator;
    }

    public function getAttributesAkyosTheme(): \RecursiveIteratorIterator
    {
        try {
            $directory = new \RecursiveDirectoryIterator($this->pathTheme);
            $iterator = new \RecursiveIteratorIterator($directory);
        } catch (\Exception $e) {
            throw new \Exception('The folder "Attributes" is missing in '.$this->pathTheme. ', please create it. 🥺');
        }

        return $iterator;
    }

    public function load(): array
    {
        $iterator = $this->getAttributesAkyosCore();
        $iteratorTheme = $this->getAttributesAkyosTheme();
        $attributes = [];
        $attributesTheme = [];
        foreach ($iterator as $info) {
            if (preg_match('/\w*[.]php\b/', $info->getFileName())) {
                require_once $info->getPathName();
                $name = explode('.', $info->getFileName());
                $name = 'Akyos\\Core\\Attributes\\'.$name[0];
                $attributes[] = $name;
            }
        }
        foreach ($iteratorTheme as $info) {
            if (preg_match('/\w*[.]php\b/', $info->getFileName())) {
                require_once $info->getPathName();
                $name = explode('.', $info->getFileName());
                $name = 'App\\Attributes\\'.$name[0];
                $attributesTheme[] = $name;
            }
        }

        return array_merge($attributes, $attributesTheme);
    }
}
