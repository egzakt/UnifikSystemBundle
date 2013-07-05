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
}
