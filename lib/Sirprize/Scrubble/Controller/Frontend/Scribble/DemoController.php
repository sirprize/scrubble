<?php

namespace Sirprize\Scrubble\Controller\Frontend\Scribble;

use Sirprize\Scribble\Filter\Criteria;
use Sirprize\Paginate\Paginator;
use Sirprize\Scrubble\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;

class DemoController extends AbstractController
{

    public function demoAction(Request $request, $template, $slug)
    {
        $criteria = new Criteria();
        $criteria->setSlug($slug);

        $repository = $this->getServices()->get('scribble.repository');
        $scribble = $repository->getOne($criteria);

        if(!$scribble)
        {
            throw new NotFoundHttpException();
        }

        $loader = new FilesystemLoader(
            array(
                $this->getServices()->get('theme')->getTemplateDir().'/%name%',
                $scribble->getDir().'/%name%'
            )
        );

        $view = new PhpEngine(new TemplateNameParser(), $loader);

        $vars = array(
            'request' => $request,
            'services' => $this->getServices(),
            'scribble' => $scribble
        );

        return new Response($view->render($template, $vars));
    }
}