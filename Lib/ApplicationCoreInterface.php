<?php

namespace Flexy\SystemBundle\Lib;

use Flexy\SystemBundle\Entity\App;
use Flexy\SystemBundle\Entity\Section;
use Flexy\SystemBundle\Lib\NavigationElementInterface;

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
    public function init();

    /**
     * Get the current section entity
     *
     * @return Section
     */
    public function getSection();

    /**
     * @param NavigationElementInterface $element
     */
    public function addNavigationElement(NavigationElementInterface $element);

    /**
     * @return NavigationElementInterface
     */
    public function getElement();

    /**
     * The associated app entity
     *
     * @return App
     */
    public function getApp();

    /**
     * The name that represent the application
     *
     * @return string
     */
    public function getAppName();
}
