<?php

namespace Egzakt\SystemBundle\Listener;

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
     * Event handler that renders not found page
     * in case of a NotFoundHttpException
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException && $this->kernel->getEnvironment() != 'dev') {

            $httpKernel = $event->getKernel();
            $response = $httpKernel->forward('EgzaktFrontendCoreBundle:Exception:error404', array(
                'exception' => $exception,
            ));

            $response->setStatusCode(404);
            $event->setResponse($response);
        } elseif ($exception instanceof AccessDeniedHttpException) {

            $httpKernel = $event->getKernel();
            $response = $httpKernel->forward('EgzaktFrontendCoreBundle:Exception:error403', array(
              'exception' => $exception,
            ));

            $response->setStatusCode(403);
            $event->setResponse($response);
        }
    }

    /**
     * Set kernel
     *
     * @param Kernel $kernel
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }
}