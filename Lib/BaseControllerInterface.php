<?php

namespace Egzakt\SystemBundle\Lib;

/**
 * Base Controller Interface
 */
interface BaseControllerInterface
{
    /**
     * Init
     *
     * @abstract
     */
    public function init();

    /**
     * Get Core
     *
     * @abstract
     */
    public function getCore();
}
