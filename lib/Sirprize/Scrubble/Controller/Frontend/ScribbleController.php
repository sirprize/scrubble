<?php

namespace Sirprize\Scrubble\Controller\Frontend;

use Sirprize\Scribble\Filter\Criteria;
use Sirprize\Paginate\Paginator;
use Sirprize\Scrubble\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScribbleController extends AbstractController
{

    public function indexAction(Request $request, $template)
    {
        $criteria = new Criteria();
        $criteria->setFind($request->query->get('find'));

        $paginator = new Paginator();
        $paginator->setPageParam('page');
        $paginator->setBaseUrl($this->getServices()->get('urler')->generate('frontendScribbleIndex'));
        $paginator->addParam('find', $criteria->getFind());

        $params = array(
            'page' => $request->query->get('page'),
            'sorting' => 'created',
            'descending' => true
        );

        $repository = $this->getServices()->get('scribble.repository');
        $scribbles = $repository->getList($criteria, $paginator, $params);
        $view = $this->getServices()->get('view');

        $vars = array(
            'request' => $request,
            'services' => $this->getServices(),
            'scribbles' => $scribbles,
            'tags' => $repository->getAllTags(),
            'tagCounts' => $repository->getAllTagCounts()
        );

        return new Response($view->render($template, $vars));
    }

    public function tagAction(Request $request, $template, $tags)
    {
        $criteria = new Criteria();
        $criteria->setTags(explode('/', $tags), 100);// limit to 100 tags to search for

        $paginator = new Paginator();
        $paginator->setPageParam('page');
        $paginator->setBaseUrl($this->getServices()->get('urler')->generate('frontendScribbleTag', array('tags' => $tags)));

        $params = array(
            'page' => $request->query->get('page'),
            'sorting' => 'creation-date',
            'descending' => true
        );

        $repository = $this->getServices()->get('scribble.repository');
        $scribbles = $repository->getList($criteria, $paginator, $params);
        $view = $this->getServices()->get('view');

        $vars = array(
            'request' => $request,
            'services' => $this->getServices(),
            'scribbles' => $scribbles,
            'tags' => $repository->getAllTags(),
            'tagCounts' => $repository->getAllTagCounts()
        );

        return new Response($view->render($template, $vars));
    }

    public function detailAction(Request $request, $template, $slug)
    {
        $criteria = new Criteria();
        $criteria->setSlug($slug);

        $repository = $this->getServices()->get('scribble.repository');
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