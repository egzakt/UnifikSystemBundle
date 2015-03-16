<?php

namespace Unifik\SystemBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectingController extends Controller
{
    /**
     * Remove the trailing slash from an URL, if not matched by any routes in the Router
     *
     * The route for this function should be at the end of the routing.yml file
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, 301);
    }
}