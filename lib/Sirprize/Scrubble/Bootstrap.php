<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Scrubble;

use Doctrine\Common\ClassLoader;
use Sirprize\Scrubble\ScrubbleException;
use Sirprize\Scrubble\Controller\ErrorController;
use Sirprize\Scribble\ScribbleDirWithSubdirs;
use Sirprize\Scrubble\Config;
use Sirprize\Scrubble\DependencyInjection\DiContainer;
use Sirprize\Scrubble\Service\Scribble\ScribbleRepository;
use Sirprize\Scrubble\Theme;
use Sirprize\Scrubble\Defaults\Routes;
use Sirprize\Scrubble\HttpKernel\HttpKernel;
use Sirprize\Scrubble\HttpKernel\EventSubscriber\ControllerServicesInjector;
use Sirprize\Scrubble\HttpKernel\EventSubscriber\ResponseFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;

/**
 * Bootstrap.
 *
 * @author Christian Hoegl <chrigu@sirprize.me>
 */
class Bootstrap
{

    public static function run($configFile)
    {
        if(!is_file($configFile) || !is_readable($configFile))
        {
            throw new \Exception(sprintf('Invalid config file: "%s"', $configFile));
        }

        require $configFile;

        if(!isset($config))
        {
            throw new \Exception(sprintf('The config file is expected to provide a variable named $config in "%s"', $configFile));
        }
        
        $getConfigItem = function($id, $default = null, $throw = false) use ($config)
        {
            if(array_key_exists($id, $config))
            {
                return $config[$id];
            }

            if($throw)
            {
                throw new \Exception(sprintf('Missing bootstrap config item "%s"', $id));
            }

            return $default;
        };

        $php = $getConfigItem('php', array());
        $displayErrors = (array_key_exists('displayErrors', $php)) ? (bool) $php['displayErrors'] : false;
        $displayStartupErrors = (array_key_exists('displayStartupErrors', $php)) ? (bool) $php['displayStartupErrors'] : false;
        $errorReporting = (array_key_exists('errorReporting', $php)) ? $php['errorReporting'] : E_ALL & ~E_NOTICE;
        $defaultTimezone = (array_key_exists('defaultTimezone', $php)) ? $php['defaultTimezone'] : 'UTC';
        $internalEncoding = (array_key_exists('internalEncoding', $php)) ? $php['internalEncoding'] : 'UTF-8';

        ini_set('display_errors', $displayErrors);
        ini_set('display_startup_errors', $displayStartupErrors);
        error_reporting($errorReporting);
        date_default_timezone_set($defaultTimezone);
        mb_internal_encoding($internalEncoding);

        foreach($getConfigItem('requires', array()) as $file)
        {
            require_once $file;
        }

        foreach($getConfigItem('namespaces', array()) as $namespace => $path)
        {
            $loader = new ClassLoader($namespace, $path);
            $loader->register();
        }

        return $config;
    }

    public static function getServices(array $config)
    {
        $services = new DiContainer();
        $services['config'] = $config;

        $services['scribble.directory'] = $services->share(function($c) {
            return new ScribbleDirWithSubdirs($c['config']['scribble.directory']);
        });

        $services['scribble.repository'] = function($c) {
            $repository = new ScribbleRepository($c['config']['scribble.repository']);
            $repository->setDirectory($c['scribble.directory']);
            return $repository;
        };

        $services['theme'] = $services->share(function($c) {
            return new Theme($c['config']['theme']);
        });

        $services['google'] = $services->share(function($c) {
            return new Config($c['config']['google']);
        });

        $services['facebook'] = $services->share(function($c) {
            return new Config($c['config']['facebook']);
        });

        // framework stuff
        $services['routes'] = $services->share(function($c) {
            return Bootstrap::getDefaultRoutes();
        });

        $services['context'] = $services->share(function($c) {
            $context = new RequestContext();
            $context->fromRequest($c['request']);
            return $context;
        });

        $services['matcher'] = $services->share(function($c) {
            return new UrlMatcher($c['routes'], $c['context']);
        });

        $services['dispatcher'] = $services->share(function($c) {
            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber(new RouterListener($c['matcher']));
            $dispatcher->addSubscriber(new ControllerServicesInjector($c));
            #$dispatcher->addSubscriber(new ResponseFilter($c));
            $dispatcher->addSubscriber(new ExceptionListener(array(new ErrorController, 'indexAction')));
            return $dispatcher;
        });

        $services['urler'] = $services->share(function($c) {
            return new UrlGenerator($c['routes'], $c['context']);
        });

        $services['resolver'] = $services->share(function($c) {
            return new ControllerResolver();
        });

        $services['kernel'] = $services->share(function($c) {
            return new HttpKernel($c['dispatcher'], $c['resolver']);
        });

        $services['request'] = $services->share(function($c) {
            $request = Request::createFromGlobals();
            $request->attributes->add(array('services' => $c));
            return $request;
        });

        $services['view'] = $services->share(function($c) {
            $loader = new FilesystemLoader($c['theme']->getTemplateDir().'/%name%');
            return new PhpEngine(new TemplateNameParser(), $loader);
        });

        return $services;
    }

    public static function getDefaultRoutes()
    {
        $routes = new RouteCollection();

        $routes->add('feedScribbleIndex', new Route(
            '/feed/scribbles/',
            array('_controller' => 'Sirprize\Scrubble\Controller\Feed\ScribbleController::atomAction', 'template' => 'feed/scribble/atom.xml')
        ));

        $routes->add('apiScribbleIndex', new Route(
            '/api/scribbles/',
            array('_controller' => 'Sirprize\Scrubble\Controller\Api\ScribbleController::indexAction')
        ));

        $routes->add('frontendTagIndex', new Route(
            '/tags/',
            array('_controller' => 'Sirprize\Scrubble\Controller\Frontend\TagController::indexAction', 'template' => 'frontend/tag/index.phtml')
        ));

        $routes->add('frontendScribbleTag', new Route(
            '/tag/{tags}/',
            array('_controller' => 'Sirprize\Scrubble\Controller\Frontend\ScribbleController::tagAction', 'template' => 'frontend/scribble/index.phtml'),
            array('tags' => '[\w\/-]+')
        ));

        $routes->add('frontendScribbleIndex', new Route(
            '/',
            array('_controller' => 'Sirprize\Scrubble\Controller\Frontend\ScribbleController::indexAction', 'template' => 'frontend/scribble/index.phtml')
        ));

        $routes->add('frontendScribbleDemoIndex', new Route(
            '/scribble/{slug}/demo/',
            array('_controller' => 'Sirprize\Scrubble\Controller\Frontend\Scribble\DemoController::demoAction', 'template' => 'demo.phtml'),
            array('slug' => '[\w-]+')
        ));

        $routes->add('frontendScribbleDetail', new Route(
            '/scribble/{slug}/',
            array('_controller' => 'Sirprize\Scrubble\Controller\Frontend\ScribbleController::detailAction', 'template' => 'frontend/scribble/detail.phtml'),
            array('slug' => '[\w-]+')
        ));

        $routes->add('facebookChannelIndex', new Route(
            '/facebook/channel/',
            array('_controller' => 'Sirprize\Scrubble\Controller\Facebook\ChannelController::indexAction')
        ));

        return $routes;
    }
}