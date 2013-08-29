<?php

namespace Egzakt\SystemBundle\Lib\Frontend;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Lib\BaseControllerInterface;

/**
 * Base Controller for all Egzakt Frontend Bundles
 */
abstract class BaseController extends Controller implements BaseControllerInterface
{
    /**
     * Construct
     */
    public function __construct()
    {

    }

    /**
     * Init
     */
    public function init()
    {

    }

    /**
     * Get the frontend core
     *
     * @return Core
     */
    public function getCore()
    {
        return $this->container->get('egzakt_frontend.core');
    }

    /**
     * Get the current section entity
     *
     * @return Section
     */
    public function getSection()
    {
        return $this->getCore()->getSection();
    }

    /**
     * Get the doctirne manager
     *
     * @return ObjectManager
     */
    public function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Registers a message for a given type.
     *
     * @param string       $type
     * @param string|array $message
     */
    protected function setFlash($type, $message) {
        $this->get('session')->getFlashBag()->set($type, $message);
    }

    /**
     * Has flash messages for a given type?
     *
     * @param string $type
     *
     * @return boolean
     */
    protected function hasFlash($type) {
        return $this->get('session')->getFlashBag()->has($type);
    }

    /**
     * Gets and clears flash from the stack.
     *
     * @param string $type
     * @param array  $default Default value if $type does not exist.
     *
     * @return array
     */
    protected function getFlash($type, array $default = array()) {
        return $this->get('session')->getFlashBag()->get($type, $default);
    }
}
