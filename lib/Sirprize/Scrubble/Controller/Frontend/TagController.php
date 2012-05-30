<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Scrubble\Controller\Frontend;

use Sirprize\Scrubble\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TagController extends AbstractController
{

    public function indexAction(Request $request, $template)
    {
        // we need all tags from all scribbles according to the current mode (published, unpublished or all)
        $params = array(
            'page' => 1,
            'itemsPerPage' => 1000 * 1000
        );

        $repository = $this->getServices()->get('scribble.repository');
        $scribbles = $repository->getList(null, $params);
        $view = $this->getServices()->get('view');

        $vars = array(
            'request' => $request,
            'services' => $this->getServices(),
            'tags' => $scribbles->getScribbles()->getTags(),
            'tagCounts' => $scribbles->getScribbles()->getTagCounts()
        );

        return new Response($view->render($template, $vars));
    }
}