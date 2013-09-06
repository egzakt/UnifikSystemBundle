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
    public function __toString();

    /**
     * Get Route
     *
     * @abstract
     */
    public function getRoute();

    /**
     * Get Route Params
     *
     * @param mixed $params Route Params
     *
     * @abstract
     */
    public function getRouteParams($params = array());

    /**
     * Is Active
     *
     * @abstract
     */
    public function isActive();

    /**
     * Is Editable
     *
     * @abstract
     */
    public function isEditable();

    /**
     * Is Deletable
     *
     * @abstract
     */
    public function isDeletable();
}
