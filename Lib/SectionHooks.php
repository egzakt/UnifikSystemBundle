<?php

namespace Flexy\SystemBundle\Lib;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Flexy\SystemBundle\Entity\Section;
use Symfony\Component\DependencyInjection\Container;

class SectionHooks
{
    /**
     * @var Registry Doctrine object (to be injected)
     */
    protected $doctrine;

    /**
     * @var Container $container
     */
    protected $container;

    /**
     * Set Doctrine
     *
     * @param Registry $doctrine The Doctrine Registry
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get Doctrine
     *
     * @return Registry;
     */
    public function getDoctrine()
    {
        return $this->doctrine;
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
     * Get Section Hooks
     *
     * Get the array of section hooks
     *
     * return array(
     *     12 => array(
     *         'repository' => 'FlexyBackendCareerBundle:Category',
     *         'method' => 'findAll',
     *         'arguments' => array(true, $param1) // Array of function parameters (e.g. $repo->findAll(true))
     *     )
     * );
     *
     * @return array
     */
    public function getSectionHooks()
    {
        return array();
    }

    /**
     * Process
     *
     * Apply the Section Hooks on Navigation Elements
     *
     * @param $sections
     */
    public function process($sections)
    {
        foreach ($this->getSectionHooks() as $sectionId => $sectionHook) {
            // Find the section in our tree
            $section = $this->findSectionInTree($sectionId, $sections);

            // If the section was found
            if ($section) {
                // Find the hooked elements
                $elements = $this->getSectionHookElements($sectionHook);

                // Set the elements as Section Hooks (merged with the section's childrens) of this section
                $section->setSectionHooks($elements);
            }
        }
    }

    /**
     * Get Section Hooks By Id
     *
     * Return hooks for a specified section ID
     *
     * @param $sectionId
     * @return array
     */
    public function getSectionHooksById($sectionId)
    {
        $sectionHooks = $this->getSectionHooks();

        if (array_key_exists($sectionId, $sectionHooks)) {
            return $sectionHooks[$sectionId];
        } else {
            return array();
        }
    }

    /**
     * Get Section Hook Elements
     *
     * Get the elements of a Section Hook
     *
     * @param $sectionHook
     *
     * @return array
     */
    public function getSectionHookElements($sectionHook)
    {
        $elements = array();

        if ($sectionHook) {
            $method = $sectionHook['method'];

            if ($sectionHook['arguments']) {
                // Call a function with parameter(s)
                $elements = call_user_func_array(array($this->getDoctrine()->getRepository($sectionHook['repository']), $method), $sectionHook['arguments']);
            } else {
                // Call a function without parameter
                $elements = call_user_func(array($this->getDoctrine()->getRepository($sectionHook['repository']), $method));
            }
        }

        // Wrap the Section Hooks elements in a NavigationItem Wrapper Class
        $wrappedElements = array();
        foreach ($elements as $element) {
            $wrappedElement = $this->container->get('flexy_system.navigation_item');
            $wrappedElement->setEntity($element);
            $wrappedElements[] = $wrappedElement;
        }

        return $wrappedElements;
    }

    /**
     * Find Section In Tree
     *
     * Find a section in the tree of sections of the current navigation
     *
     * @param integer $sectionId
     * @param array   $elements
     *
     * @return object
     */
    public function findSectionInTree($sectionId, $elements)
    {
        // Loop through the elements (it's not necessary a Section object)
        foreach ($elements as $element) {
            // Is it a Section|SectionProxy and does the ID match?
            if ($element->getEntity()->getId() == $sectionId && ($element->getEntity() instanceof Section || strstr(get_class($element->getEntity()), 'FlexyBackendSectionBundleEntitySectionProxy'))) {
                // Return this element
                return $element;
            }

            // The ID doesn't match, let's take a look in the childrens
            if ($element->hasChildren()) {
                $section = $this->findSectionInTree($sectionId, $element->getChildren());
                if ($section) {
                    // Matched this section
                    return $section;
                }
            }
        }
    }

}
