<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Scrubble\HttpKernel\EventSubscriber;

use Sirprize\Scrubble\DependencyInjection\DiContainer;
use Sirprize\Scrubble\Controller\ControllerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ControllerServicesInjector implements EventSubscriberInterface
{
    protected $services = null;

    public function __construct(DiContainer $services)
    {
        $this->services = $services;
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => array('onController', -100));
    }

    public function onController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $this->setServices($controller[0]);
    }

    protected function setServices(ControllerInterface $controller)
    {
        $controller->setServices($this->services);
    }
}