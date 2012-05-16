<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Scrubble\Controller\Feed;

use Sirprize\Scribble\Filter\Criteria;
use Sirprize\Paginate\Paginator;
use Sirprize\Scrubble\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScribbleController extends AbstractController
{

    public function atomAction(Request $request, $template)
    {
        $params = array(
            'sorting' => 'created',
            'descending' => true
        );

        $repository = $this->getServices()->get('scribble.repository');
        $scribbles = $repository->getList(new Criteria(), new Paginator(), $params);
        $view = $this->getServices()->get('view');

        $vars = array(
            'request' => $request,
            'services' => $this->getServices(),
            'scribbles' => $scribbles
        );

        return new Response($view->render($template, $vars));
    }

}