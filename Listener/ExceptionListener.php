<?php

namespace Flexy\SystemBundle\Listener;

use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Exception Listener
 */
class ExceptionListener
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var TimedTwigEngine
     */
    private $templating;

    /**
     * Event handler that renders custom pages in case of a NotFoundHttpException (404)
     * or a AccessDeniedHttpException (403).
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ('dev' == $this->kernel->getEnvironment()) {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {

            $response = $this->templating->renderResponse('FlexySystemBundle:Frontend/Exception:404.html.twig');
            $response->setStatusCode(404);

            $event->setResponse($response);

        } elseif ($exception instanceof AccessDeniedHttpException) {

            $response = $this->templating->renderResponse('FlexySystemBundle:Frontend/Exception:403.html.twig');
            $response->setStatusCode(403);

            $event->setResponse($response);
        }
    }

    /**
     * @param Kernel $kernel
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param TimedTwigEngine $templating
     */
    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }
}
