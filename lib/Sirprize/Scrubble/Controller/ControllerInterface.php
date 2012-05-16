<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Scrubble\Controller;

use Sirprize\Scrubble\DependencyInjection\DiContainer;

interface ControllerInterface
{
    public function setServices(DiContainer $services);
}