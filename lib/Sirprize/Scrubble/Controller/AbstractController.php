<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Scrubble\Controller;

use Sirprize\Scrubble\DependencyInjection\DiContainer;

abstract class AbstractController implements ControllerInterface
{
    protected $services = null;

    public function setServices(DiContainer $services)
    {
        $this->services = $services;
        return $this;
    }

    protected function getServices()
    {
        return $this->services;
    }
}