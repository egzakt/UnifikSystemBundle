<?php

namespace Egzakt\SystemBundle\Lib;

use Egzakt\SystemBundle\Entity\App;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Lib\NavigationElementInterface;

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
     * @param NavigationElementInterface $element
     */
    function addNavigationElement(NavigationElementInterface $element);

    /**
     * @return NavigationElementInterface
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