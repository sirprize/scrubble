<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Scrubble;

/**
 * Env holds essential environment settings.
 *
 * @author Christian Hoegl <chrigu@sirprize.me>
 */
class Env
{

    protected $debug = null;
    protected $baseDir = null;
    protected $libDir = null;
    protected $templateDir = null;
    protected $vendorIncludeDir = null;
    protected $basePath = null;
    protected $mediaPath = null;
    protected $vendorMediaPath = null;

    public function __construct(array $config)
    {
        $this->debug = $this->getConfigItem($config, 'debug');
        $this->baseDir = $this->getConfigItem($config, 'baseDir');
        $this->libDir = $this->getConfigItem($config, 'libDir');
        $this->templateDir = $this->getConfigItem($config, 'templateDir');
        $this->vendorIncludeDir = $this->getConfigItem($config, 'vendorIncludeDir');
        $this->basePath = $this->getConfigItem($config, 'basePath');
        $this->mediaPath = $this->getConfigItem($config, 'mediaPath');
        $this->vendorMediaPath = $this->getConfigItem($config, 'vendorMediaPath');
    }

    public function debug()
    {
        return $this->debug;
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    public function getLibDir()
    {
        return $this->libDir;
    }

    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    public function getVendorIncludeDir()
    {
        return $this->vendorIncludeDir;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function getMediaPath()
    {
        return $this->mediaPath;
    }

    public function getVendorMediaPath()
    {
        return $this->vendorMediaPath;
    }

    protected function getConfigItem(array $config, $id)
    {
        if(!isset($config[$id]))
        {
            throw new ScribbleException(sprintf('Missing item "%s"', $id));
        }

        return $config[$id];
    }

}