<?php

namespace Flexy\SystemBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Router;

/**
 * Response Listener
 */
class ResponseListener
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Router
     */
    private $router;

    /**
     * On kernel response event
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        // Only master request and 200 OK are processed
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType() && $event->getResponse()->isOk()) {

            $route = $this->router->match($this->request->getPathInfo());

            // Ignore internal route
            if (0 === stripos($route['_route'], '_')) {
                return;
            }

            $this->session->set('_flexy.last_master_request_uri', $this->request->getUri());
            $this->session->set('_flexy.last_master_request_route', $route);
        }
    }

    /**
     * @param Session $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }
}
