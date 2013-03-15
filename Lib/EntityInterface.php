<?php

namespace Egzakt\SystemBundle\Lib;

/**
 * Entity Interface
 */
interface EntityInterface
{
    /**
     * __toString
     *
     * @abstract
     */
    function __toString();

    /**
     * Get Route
     *
     * @abstract
     */
    function getRoute();

    /**
     * Get Route Params
     *
     * @param mixed $params Route Params
     *
     * @abstract
     */
    function getRouteParams($params = array());

    /**
     * Is Active
     *
     * @abstract
     */
    function isActive();

    /**
     * Is Editable
     *
     * @abstract
     */
    function isEditable();

    /**
     * Is Deletable
     *
     * @abstract
     */
    function isDeletable();
}