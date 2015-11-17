<?php

namespace Unifik\SystemBundle\Lib;

use Symfony\Component\DependencyInjection\Container;

use Unifik\SystemBundle\Lib\NavigationItemInterface;
use Unifik\SystemBundle\Entity\Section;

/**
 * Navigation Builder
 *
 * @throws \Exception
 */
class NavigationBuilder
{
    /**
     * @var array
     */
    protected $elements;          // A collection of wrapped elements representing multi-level hierarchical data

    /**
     * @var mixed
     */
    protected $maxLevel;          // The maximum level of the navigation (or false if no maximum)

    /**
     * @var object
     */
    protected $selectedElement;   // The element to be set as selected in the navigation tree

    /**
     * @var Container $container
     */
    protected $container;

    /**
     * @var mixed $coreElements
     */
    protected $coreElements;

    /**
     * Launch the build of the navigation tree from the first level
     */
    public function build()
    {
        $elements = $this->buildLevel($this->elements);
        $this->elements = $elements;
    }

    /**
     *  Recursively build the navigation elements from a certain level
     *
     * @param array $elements An array of wrapped objects (NavigationItem)
     * @param null  $parent   The parent Element (for recursivity)
     * @param int   $level    The actual level (for recursivity)
     *
     * @throws \Exception
     *
     * @return array
     */
    private function buildLevel($elements, $parent = null, $level = 1)
    {
        foreach ($elements as $key => $element) {

            if (false == $element instanceof NavigationItemInterface) {
                throw new \Exception(get_class($element) . ' need to implement the NavigationItemInterface to be usable in the ' . get_class($this));
            }

            if (!$element->getParent()) {
                $element->setParent($parent);
            }

            $element->setSelected(get_class($element->getEntity()) == get_class($this->selectedElement) && $element->getEntity()->getId() == $this->selectedElement->getId());

            // TODO Remplacer le 1 par le ID de la section 'Accueil'
            if (get_class($element->getEntity()) == 'Unifik\Backend\SectionBundle\Entity\Section' && $element->getEntity()->getId() == 1 && $element->isSelected() && ($this->container->get('unifik_system.core')->getCurrentAppName() != 'backend')) {
                unset($elements[$key]);
                continue;
            }

            // This is the currently selected element
            // We set every parents of the currently selected element as selected from the current level back to level 1
            if ($element->isSelected()) {

                $parent = $element->getParent();

                while ($parent && $parent->getEntity()->getId()) {
                    $parent->setSelected(true);
                    $parent = $parent->getParent();
                }
            }

            if (!$this->maxLevel || $level < $this->maxLevel) {
                // This element have some children, we start the same process on the children collection
                if ($element->hasChildren()) {
                    $this->buildLevel($element->getChildren(), $element, ($level + 1));
                }
            }

        }

        return $elements;
    }

    /**
     * Set Elements
     *
     * @param array $elements An array of elements
     */
    public function setElements($elements, $checkActive = false, $maxLevel = false)
    {
        $this->maxLevel = $maxLevel;

        // Wrap elements in a NavigationItem Wrapper Class
        $wrappedElements = $this->buildNavigationItems($elements, null, $checkActive);

        $this->elements = $wrappedElements;
    }

    /**
     * Get Elements
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Add elements to the existing array
     *
     * @param array $elements An array of elements
     */
    public function addElements($elements, $maxLevel = false)
    {
        $this->maxLevel = $maxLevel;

        // Wrap elements in a NavigationItem Wrapper Class
        $wrappedElements = $this->buildNavigationItems($elements);

        if ($this->elements) {
            $this->elements = array_merge($this->elements, $wrappedElements);
        } else {
            $this->elements = $wrappedElements;
        }
    }

    /**
     * Add a single element to the existing array
     *
     * @param $element
     */
    public function addElement($element)
    {
        // Wrap element in a NavigationItem Wrapper Class
        $wrappedElement = $this->buildNavigationItems(array($element));

        $this->elements[] = $wrappedElement[0];
    }

    /**
     * Build Navigation Items
     *
     * Wraps all navigation elements in a NavigationItem Wrapper Class
     *
     * @param array          $elements
     * @param NavigationItem $parentElement
     *
     * @return array
     */
    protected function buildNavigationItems($elements, $parentElement = null, $checkActive = false, $level = 1)
    {
        $wrappedElements = array();

        foreach ($elements as $element) {

            // Check si la section est active
            if ($checkActive && is_callable(array($element, 'getActive')) && !$element->getActive()) {
                continue;
            }

            // Create the NavigationItem wrapper
            $wrappedElement = $this->buildNavigationItem($element);

            // Set the parent element and add the children to this parent element
            if ($parentElement) {
                $wrappedElement->setParent($parentElement);
                $parentElement->addChildren($wrappedElement);
            }

            if (!$this->maxLevel || $level < $this->maxLevel) {
                // Recursive call
                if ($element->hasChildren()) {
                    $this->buildNavigationItems($element->getChildren(), $wrappedElement, $checkActive, ($level + 1));
                }
            }

            $wrappedElements[] = $wrappedElement;
        }

        return $wrappedElements;
    }

    /**
     * Build Navigation Item
     *
     * Wraps a single navigation element in a NavigationItem Wrapper Class
     *
     * @param mixed $element
     *
     * @return NavigationItem
     */
    protected function buildNavigationItem($element)
    {
        $wrappedElement = $this->container->get('unifik_system.navigation_item');
        $wrappedElement->setEntity($element);

        return $wrappedElement;
    }

    /**
     * Is Section Element
     *
     * Whether this element is a Section entity or not
     *
     * @param $element
     *
     * @return bool
     */
    protected function isSectionElement($element)
    {
        return ($element instanceof Section || strstr(get_class($element), 'UnifikBackendSectionBundleEntitySectionProxy'));
    }

    /**
     * Set selected element
     *
     * @param NavigationElementInterface $selectedElement The element to select
     */
    public function setSelectedElement(NavigationElementInterface $selectedElement)
    {
        $this->selectedElement = $selectedElement;
    }

    /**
     * Get selected Element
     *
     * @return object
     */
    public function getSelectedElement()
    {
        return $this->selectedElement;
    }

    /**
     * Set Container
     *
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Get Container
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set Core Elements
     *
     * Core Elements are used apply the selected state on Section Hooks
     *
     * @param mixed $coreElements
     */
    public function setCoreElements($coreElements)
    {
        $this->coreElements = $coreElements;
    }

    /**
     * Get Core Elements
     *
     * @return mixed
     */
    public function getCoreElements()
    {
        return $this->coreElements ?: array();
    }
}
