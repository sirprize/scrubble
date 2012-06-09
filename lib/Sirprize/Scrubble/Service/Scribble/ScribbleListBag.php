<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Scrubble\Service\Scribble;

use Sirprize\Scribble\ScribbleCollection;
use Sirprize\Scribble\Filter\Filter;
use Sirprize\Scribble\Filter\Criteria;
use Sirprize\Paginate\Paginator;

/**
 * ScribbleList bags list objects in one place.
 *
 * @author Christian Hoegl <chrigu@sirprize.me>
 */
class ScribbleListBag
{

    protected $paginator = null;
    protected $criteria = null;
    protected $scribbles = null;
    protected $relatedTags = null;
    protected $relatedTagCounts = null;

    public function __construct(ScribbleCollection $scribbles, Filter $filter, Criteria $criteria, Paginator $paginator)
    {
        $this->paginator = $paginator;
        $this->criteria = $criteria;
        $this->scribbles = $scribbles;
        $this->relatedTags = $filter->getRelatedTags();
        $this->relatedTagCounts = $filter->getRelatedTagCounts();
    }

    public function getPaginator()
    {
        return $this->paginator;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function getScribbles()
    {
        return $this->scribbles;
    }

    public function getRelatedTags()
    {
        return $this->relatedTags;
    }

    public function getRelatedTagCounts()
    {
        return $this->relatedTagCounts;
    }
}