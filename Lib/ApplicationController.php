<?php

namespace Egzakt\SystemBundle\Lib;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class ApplicationController extends Controller implements BaseControllerInterface
{

    /**
     * @inheritdoc
     */
    public function init()
    {

    }

    /**
     * Return the core.
     *
     * @return ApplicationCoreInterface
     */
    abstract public function getCore();

    /**
     * @return Core
     */
    protected function getSystemCore()
    {
        return $this->get('egzakt_system.core');
    }

    /**
     * Get the current section entity
     * @deprecated
     * @return Section
     */
    protected function getSection()
    {
        return $this->getCore()->getSection();
    }

    /**
     * Get the current section entity
     * @return Section
     */
    protected function getCurrentSection()
    {
        return $this->getCore()->getSection();
    }

    /**
     * Get the current app entity
     *
     * @return App
     */
    protected function getApp()
    {
        return $this->getCore()->getApp();
    }

    /**
     * Get the current app name
     *
     * @return string
     */
    protected function getCurrentAppName()
    {
        return $this->getSystemCore()->getCurrentAppName();
    }

    /**
     * Get the Entity Manager
     * @deprecated
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param $name
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($name)
    {
        return $this->getDoctrine()->getRepository($name);
    }


    /**
     * Adds a flash message for type.
     *
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $this->get('session')->getFlashBag()->add($type, $message);
    }

}