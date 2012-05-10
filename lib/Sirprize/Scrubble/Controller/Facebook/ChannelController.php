<?php

namespace Sirprize\Scrubble\Controller\Facebook;

use Sirprize\Scrubble\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChannelController extends AbstractController
{

    public function indexAction(Request $request)
    {
        $cacheExpire = 60 * 60 * 24 * 365;

        $response = new Response();
        $response->setContent('<script src="http://connect.facebook.net/en_US/all.js"></script>');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'max-age='.$cacheExpire);
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $cacheExpire) . ' GMT');
        return $response;
    }
}