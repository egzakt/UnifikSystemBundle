<?php

namespace Egzakt\SystemBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\Container;

use Egzakt\SystemBundle\Lib\Core;
use Egzakt\SystemBundle\Lib\BaseControllerInterface;

/**
 * Controller Listener
 */
class ControllerListener
{
    /**
     * @var Container
     */
    private $container;

    /**
     * On Kernel Controller
     *
     * @param FilterControllerEvent $event
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $controller = $controller[0];

        $request = $this->container->get('request');

        if (false == $request->get('_egzaktEnabled')) {
            return;
        }

        $egzaktRequest = $request->get('_egzaktRequest');
        $applicationName = $egzaktRequest['appName'];

        if (false == $applicationName) {
            return;
        }

        // sectionId juggling to make the backend parameters behave like the frontend router auto generated parameters
        if ('backend' === $applicationName) {
            if ($sectionId = $request->get('sectionId', $request->get('section_id'))) { // BC check
                $egzaktRequest = $request->get('_egzaktRequest');
                $egzaktRequest['sectionId'] = $sectionId;
                $request->attributes->set('_egzaktRequest', $egzaktRequest);
            }
        }

        if (false == $controller instanceof BaseControllerInterface) {
            throw new \Exception(get_class($controller) . ' must extends the Egzakt/SystemBundle/Lib/' . ucfirst($applicationName) . '/BaseController class.');
        }

        $applicationName = strtolower($applicationName);
        $systemCore = $this->container->get('egzakt_system.core');
        $applicationCore = $this->container->get('egzakt_' . $applicationName . '.core');
        $systemCore->setApplicationCore($applicationCore);

        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $systemCore->init();
            $applicationCore->init();
        }

        $controller->init();
    }

    /**
     * Set Container
     *
     * @param Container $container The Container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}