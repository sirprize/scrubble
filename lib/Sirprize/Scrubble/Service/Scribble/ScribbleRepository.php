<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Scrubble\Service\Scribble;

use Sirprize\Scribble\ScribbleDirWithSubdirs;
use Sirprize\Scribble\ScribbleCollection;
use Sirprize\Scribble\Filter\Filter;
use Sirprize\Scribble\Filter\Criteria;
use Sirprize\Paginate\Paginator;

/**
 * ScribbleRepository fetches scribbles.
 *
 * @author Christian Hoegl <chrigu@sirprize.me>
 */
class ScribbleRepository
{

    protected $mode = null;
    protected $itemsPerPage = null;
    protected $directory = null;

    public function __construct(array $config)
    {
        $this->itemsPerPage = (array_key_exists('itemsPerPage', $config)) ? $config['itemsPerPage'] : 20;
        $this->mode = (array_key_exists('mode', $config)) ? $config['mode'] : Criteria::MODE_PUBLISHED;
    }

    public function setDirectory(ScribbleDirWithSubdirs $directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function getList(Criteria $criteria, Paginator $paginator, array $params = array())
    {
        $page = (array_key_exists('page', $params)) ? (int) $params['page'] : 1;
        $sorting = (array_key_exists('sorting', $params)) ? $params['sorting'] : 'created';
        $descending = (array_key_exists('descending', $params)) ? (bool) $params['descending'] : true;

        // overwrite config defaults?
        $itemsPerPage = (array_key_exists('itemsPerPage', $params)) ? (int) $params['itemsPerPage'] : $this->itemsPerPage;
        $mode = ($criteria->getMode()) ? $criteria->getMode() : $this->mode;

        // criteria
        $criteria->setMode($mode);

        // get all scribbles
        $allScribbles = $this->getDirectory()->load()->getScribbles();

        // set sorting
        if($sorting == 'created')
        {
            $allScribbles->sortByCreationDate($descending);
        }
        else if($sorting == 'modified')
        {
            $allScribbles->sortByModificationDate($descending);
        }
        else if($sorting == 'slug')
        {
            $allScribbles->sortBySlug($descending);
        }

        // filter and paginate
        $filter = new Filter();
        $scribbles = $filter->apply($allScribbles, $criteria)->getScribbles();
        $paginator->calculateFromCurrentPage($scribbles->count(), $page, $itemsPerPage);
        $scribbles = new ScribbleCollection($scribbles->slice($paginator->getOffset(), $itemsPerPage));
        return new ScribbleListBag($scribbles, $filter, $criteria, $paginator);
    }

    public function getOne(Criteria $criteria)
    {
        // overwrite config defaults?
        $mode = ($criteria->getMode()) ? $criteria->getMode() : $this->mode;

        // criteria
        $criteria->setMode($mode);

        // get all scribbles
        $allScribbles = $this->getDirectory()->load()->getScribbles();

        // filter and paginate
        $filter = new Filter();
        $scribbles = $filter->apply($allScribbles, $criteria)->getScribbles();
        return $scribbles->first();
    }

    public function getAllTags()
    {
        $this->getDirectory()->load();
        return $this->getDirectory()->getScribbles()->getTags();
    }

    public function getAllTagCounts()
    {
        $this->getDirectory()->load();
        return $this->getDirectory()->getScribbles()->getTagCounts();
    }

    protected function getDirectory()
    {
        return $this->directory;
    }
}