<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Scrubble;

/**
 * Config is a configuration container.
 *
 * @author Christian Hoegl <chrigu@sirprize.me>
 */
class Config
{

    protected $config = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getItem($id, $default = null, $throw = false)
    {
        if(array_key_exists($id, $this->config))
        {
            return $this->config[$id];
        }

        if($throw)
        {
            throw new ScrubbleException(sprintf('Missing config item "%s"', $id));
        }

        return $default;
    }

}