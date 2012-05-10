<?php

namespace Sirprize\Scrubble\Controller;

use Sirprize\Scrubble\DependencyInjection\DiContainer;

interface ControllerInterface
{
    public function setServices(DiContainer $services);
}