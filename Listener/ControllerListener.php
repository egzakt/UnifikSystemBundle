<?php

namespace Egzakt\SystemBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Bridge\Monolog\Logger;

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
     * @var Core
     */
    private $systemCore;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * On Kernel Controller
     *
     * @param FilterControllerEvent $event
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
//        $entityManager->setContainer($this->container);

        $controller = $event->getController();
        $controller = $controller[0];

        // This listener only work on egzakt compatible controllers
        // TODO: Trigger using a routing parameter instead of relying on the namespace of the controller
        if (false == $controller instanceof BaseControllerInterface) {
            return;
        }

        $reflector = new \ReflectionClass(get_class($controller));
        $controllerNamespace = $reflector->getNamespaceName();
        $controllerNamespaceTokens = $this->getTokenizedControllerName($controllerNamespace);
        $applicationName = isset($controllerNamespaceTokens[2]) ? $controllerNamespaceTokens[2] : '';
        $applicationName = strtolower($applicationName);
        $applicationCore = $this->container->get('egzakt_' . $applicationName . '.core');
        $applicationCore->setRequestType($event->getRequestType());

        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->systemCore->init();
            $applicationCore->init();
        }

        $controller->init();
    }

    /**
     * Add Application Core
     *
     * @deprecated
     * @param string $name Application Name
     * @param Core   $core The Core
     */
    public function addApplicationCore($name, $core)
    {
        // deprecated
    }

    /**
     * Set Logger
     *
     * @param Logger $logger The Logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set System Core
     *
     * @param Core $core The Core
     */
    public function setSystemCore($core)
    {
        $this->systemCore = $core;
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

    /**
     * Input controller class name
     *
     * @param string $name Controller name (example name format: Egzakt\HomeBundle\Controller\Frontend)
     * @return array
     * @throws \Exception
     */
    private function getTokenizedControllerName($name)
    {
        $tokens = array();

        if (false == preg_match('/^(Egzakt|Extend)\\\\.*\\\\([A-Z][a-z]+)$/', $name, $tokens)) {
            throw new \Exception('EgzaktSystem: can\'t boot SystemCore using ' . $name . ' controller');
        }

        return $tokens;
    }
}