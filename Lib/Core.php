<?php

namespace Flexy\SystemBundle\Lib;

/**
 * SystemCore
 */
class Core
{
    /**
     * @var ApplicationCoreInterface
     */
    protected $applicationCore;

    /**
     * @var string
     */
    protected $currentAppName;

    /**
     * Init
     */
    public function init()
    {
        // ...
    }

    public function isLoaded()
    {
        if ($this->applicationCore) {
            return true;
        }
    }

    /**
     * @param ApplicationCoreInterface $applicationCore
     */
    public function setApplicationCore($applicationCore)
    {
        $this->applicationCore = $applicationCore;
    }

    /**
     * @return ApplicationCoreInterface
     */
    public function getApplicationCore()
    {
        return $this->applicationCore;
    }

    /**
     * Get Current App Name
     *
     * @return string
     */
    public function getCurrentAppName()
    {
        if ($this->currentAppName) {
            return $this->currentAppName;
        }

        return $this->applicationCore->getAppName();
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
