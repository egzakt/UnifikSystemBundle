<?php

namespace Flexy\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Breadcrumbs
 */
class Breadcrumbs
{

    /**
     * @var ArrayCollection
     */
    private $elements;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    /**
     * Add an element to Breadcrumbs
     *
     * @param object $element The object to add
     */
    public function addElement($element)
    {
        $this->getElements()->add($element);
    }

    /**
     * Remove a navigation element
     *
     * @param object $element
     */
    public function removeElement($element)
    {
        $this->getElements()->removeElement($element);
    }

    /**
     * Gets an array of elements
     *
     * @return ArrayCollection
     */
    public function getElements()
    {
        return $this->elements;
    }
}
