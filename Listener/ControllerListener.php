<?php

namespace Unifik\SystemBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\Container;

use Unifik\SystemBundle\Lib\Core;
use Unifik\SystemBundle\Lib\BaseControllerInterface;

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

        if (false == $request->get('_unifikEnabled')) {
            return;
        }

        $unifikRequest = $request->get('_unifikRequest');
        $applicationName = $unifikRequest['appCode'];
        $applicationName = strtolower(str_replace(array('-', ' '), array('_', '_'), $applicationName));

        if (false == $applicationName) {
            return;
        }

        // sectionId juggling to make the backend parameters behave like the frontend router auto generated parameters
        if ('backend' === $applicationName) {
            if ($sectionId = $request->get('sectionId')) {
                $unifikRequest = $request->get('_unifikRequest');
                $unifikRequest['sectionId'] = $sectionId;
                $request->attributes->set('_unifikRequest', $unifikRequest);
            }
        }

        if (false == $controller instanceof BaseControllerInterface) {
            throw new \Exception(get_class($controller) . ' must extends the Unifik/SystemBundle/Lib/' . ucfirst($applicationName) . '/BaseController class.');
        }

        $systemCore = $this->container->get('unifik_system.core');
        $applicationCore = $this->container->get('unifik_' . $applicationName . '.core');
        $systemCore->setApplicationCore($applicationCore);

        // Initialization of cores stack in order of: system, application and controller
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $systemCore->init();
            $applicationCore->init();
        }

        // When the controller init method return a response, this response is used and controller action is skipped
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
