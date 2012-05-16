<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Scrubble\Controller;

use Symfony\Component\HttpKernel\Exception\FlattenException;
use Sirprize\Scrubble\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{

    public function indexAction(Request $request, FlattenException $exception)
    {
        $view = $this->getServices()->get('view');

        $vars = array(
            'request' => $request,
            'services' => $this->getServices(),
            'exception' => $exception
        );

        return new Response($view->render('error.phtml', $vars));
    }
}