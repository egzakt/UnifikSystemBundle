<?php

namespace Egzakt\SystemBundle\Lib;

use Egzakt\SystemBundle\Entity\App;
use Egzakt\SystemBundle\Entity\Section;

/**
 * Base Controller Interface
 */
interface ApplicationCoreInterface
{
    /**
     * Init
     *
     * @abstract
     */
    function init();

    /**
     * Get the current section entity
     *
     * @return Section
     */
    function getSection();

    /**
     * @param NavigationInterface $element
     */
    function addNavigationElement(NavigationInterface $element);

    /**
     * @return NavigationInterface
     */
    function getElement();

    /**
     * The associated app entity
     *
     * @return App
     */
    function getApp();

    /**
     * The name that represent the application
     *
     * @return string
     */
    function getAppName();
}