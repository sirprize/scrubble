<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */
 
namespace Sirprize\Scrubble\Controller\Api;

use Sirprize\Scribble\Filter\Criteria;
use Sirprize\Scrubble\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScribbleController extends AbstractController
{

    public function indexAction(Request $request)
    {
        $criteria = new Criteria();
        $criteria->setFind($request->query->get('find'));
        
        $params = array(
            'page' => $request->query->get('page'),
            'sorting' => 'created',
            'descending' => true
        );

        $repository = $this->getServices()->get('scribble.repository');
        $scribbles = $repository->getList($criteria, $params);
        $scribbles->getPaginator()->setPageParam('page');
        $scribbles->getPaginator()->setBaseUrl($this->getServices()->get('urler')->generate('frontendScribbleIndex', array(), true));
        $scribbles->getPaginator()->addParam('find', $criteria->getFind());

        $data = array();

        foreach($scribbles->getScribbles() as $scribble)
        {
            $data[] = array(
                'title' => $scribble->getTitle(),
                'lede' => $scribble->getLede(),
                'url' => $this->getServices()->get('urler')->generate('frontendScribbleDetail', array('slug' => $scribble->getSlug()), true),
                'created' => $scribble->getCreated()->format('c'),
                'modified' => $scribble->getModified()->format('c')
            );
        }

        $response = new Response();
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}