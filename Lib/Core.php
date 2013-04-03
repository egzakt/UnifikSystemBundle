<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

/**
 * Core
 */
class Core
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $currentAppName;

    /**
     * Init
     */
    public function init()
    {
        // ...
    }

    /**
     * Set Request
     *
     * @param Request $request The Request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Set Doctrine
     *
     * @param Registry $doctrine The Doctrine Registry
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
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
     * Get Current App Name
     *
     * @return string
     */
    public function getCurrentAppName()
    {
        // TODO HACK
        return 'backend';

        if ($this->currentAppName) {
            return $this->currentAppName;
        }

        // Match each camel cased token
        $controller = str_replace('\\', '', $this->request->get('_controller'));
        $tokens = preg_split('/(?<=\\w)(?=[A-Z])/', $controller);

        if (isset($tokens[1])) {
            return strtolower($tokens[1]);
        }

        return null;
    }

    /**
     * Get Current App Name
     *
     * @param $appName
     */
    public function setCurrentAppName($appName)
    {
        $this->currentAppName = $appName;
    }
}
