<?php

namespace Flexy\SystemBundle\Lib;

/**
 * Page Title
 */
class PageTitle
{
    /**
     * @var array
     */
    private $elements;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->elements = array();
    }

    /**
     * Add Element
     *
     * @param object $element The element to add
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
     * Get Elements
     *
     * @return array
     */
    public function getElements()
    {
        $elements = array_reverse($this->elements, true);

        return $elements;
    }
}
