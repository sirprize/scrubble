<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Scrubble\Controller\Frontend;

use Sirprize\Scribble\Filter\Criteria;
use Sirprize\Scrubble\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends AbstractController
{

    public function indexAction(Request $request, $template, $slug)
    {
        $criteria = new Criteria();
        $criteria->setSlug($slug);

        $repository = $this->getServices()->get('page.repository');
        $scribble = $repository->getOne($criteria);

        if(!$scribble)
        {
            throw new NotFoundHttpException();
        }

        $view = $this->getServices()->get('view');

        $vars = array(
            'request' => $request,
            'services' => $this->getServices(),
            'scribble' => $scribble
        );

        return new Response($view->render($template, $vars));
    }
}