<?php

namespace Egzakt\SystemBundle\Lib;

/**
 * Breadcrumbs
 */
class Breadcrumbs
{
    private $elements;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->elements = array();
    }

    /**
     * Add an element to Breadcrumbs
     *
     * @param object $element The object to add
     */
    public function addElement($element)
    {
        $this->elements[] = $element;
    }

    /**
     * Remove a navigation element
     *
     * @param object $element
     */
    public function removeElement($element)
    {
        foreach ($this->elements as $k => $existingElement) {
            if ($element == $existingElement) {
                unset($this->elements[$k]);
                break;
            }
        }
    }

    /**
     * Gets an array of elements
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }
}
