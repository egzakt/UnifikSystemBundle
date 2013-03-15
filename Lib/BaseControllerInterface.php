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
    function init();

    /**
     * Get Core
     *
     * @abstract
     */
    function getCore();
}