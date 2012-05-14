<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Scrubble;

/**
 * Theme holds theme settings.
 *
 * @author Christian Hoegl <chrigu@sirprize.me>
 */
class Theme extends Config
{

    protected $debug = null;
    protected $templateDir = null;
    protected $mediaPath = null;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->debug = $this->getItem('debug', null, true);
        $this->templateDir = realpath($this->getItem('templateDir', null, true));
        $this->mediaPath = $this->getItem('mediaPath', null, true);
    }

    public function debug()
    {
        return $this->debug;
    }

    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    public function getMediaPath()
    {
        return $this->mediaPath;
    }
}