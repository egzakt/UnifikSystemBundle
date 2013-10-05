<?php

namespace Flexy\SystemBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\Container;

use Flexy\SystemBundle\Lib\Core;
use Flexy\SystemBundle\Lib\BaseControllerInterface;

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
     * @param  FilterControllerEvent $event
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $controller = $controller[0];

        $request = $this->container->get('request');

        if (false == $request->get('_flexyEnabled')) {
            return;
        }

        $flexyRequest = $request->get('_flexyRequest');
        $applicationName = $flexyRequest['appName'];
        $applicationName = strtolower($applicationName);

        if (false == $applicationName) {
            return;
        }

        // sectionId juggling to make the backend parameters behave like the frontend router auto generated parameters
        if ('backend' === $applicationName) {
            if ($sectionId = $request->get('sectionId')) {
                $flexyRequest = $request->get('_flexyRequest');
                $flexyRequest['sectionId'] = $sectionId;
                $request->attributes->set('_flexyRequest', $flexyRequest);
            }
        }

        if (false == $controller instanceof BaseControllerInterface) {
            throw new \Exception(get_class($controller) . ' must extends the Flexy/SystemBundle/Lib/' . ucfirst($applicationName) . '/BaseController class.');
        }

        $systemCore = $this->container->get('flexy_system.core');
        $applicationCore = $this->container->get('flexy_' . $applicationName . '.core');
        $systemCore->setApplicationCore($applicationCore);

        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $systemCore->init();
            $applicationCore->init();
        }

        // Bypassing the controller default process when the init method return a response
        if ($initResponse = $controller->init()) {
            $event->setController(function() use ($initResponse) {
                return $initResponse;
            });
        }
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
