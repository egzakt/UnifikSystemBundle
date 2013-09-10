<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;

class DeletableService
{

    /**
     * @var ArrayCollection
     *
     */
    private $listeners;

    /**
     * @var ArrayCollection
     */
    private $errors;

    public function __construct()
    {
        $this->listeners = new ArrayCollection();
        $this->errors = new ArrayCollection();
    }

    /**
     * Check if this entity can be deleted or not.
     * We run through a list a listeners and if a listener fail, then the entity is not deletable.
     * Listeners are attached to the service and bound to the entity class name.
     *
     * @param  Object          $entity
     * @return DeletableResult
     */
    public function checkDeletable($entity)
    {
        $classname = get_class($entity);

        if (!$this->getListeners()->containsKey($classname)) {
            return $this->createDeletableResult();
        }

        foreach ($this->getListeners()->get($classname) as $listener) {
            if (!$listener->isDeletable($entity)) {
                $this->setErrors($listener->getErrors());

                return $this->createFailResult();
            }
        }

        return $this->createDeletableResult();
    }

    /**
     *
     * Add a new listener.
     *
     * @param DeletableListenerInterface $listener
     * @param $classname
     */
    public function addListener(DeletableListenerInterface $listener, $classname)
    {

        $listeners = $this->getListeners()->get($classname);
        if (null === $listeners) {
            $listeners = new ArrayCollection();
            $this->getListeners()->set($classname, $listeners);
        }

        $listeners->add($listener);
    }

    /**
     * Return a DeletableResult with a Fail status and errors list.
     *
     * @return DeletableResult
     */
    protected function createFailResult()
    {
        return new DeletableResult(DeletableResult::STATUS_FAIL, 'Entity can\'t be deleted.', $this->getErrors());
    }

    /**
     * Return a DeletableResult with a Deletable status.
     *
     * @return DeletableResult
     */
    protected function createDeletableResult()
    {
        return new DeletableResult(DeletableResult::STATUS_DELETABLE, 'Entity can be deleted.');
    }

    /**
     * @return ArrayCollection
     */
    protected function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param ArrayCollection $errors
     */
    protected function setErrors(ArrayCollection $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return ArrayCollection
     */
    protected function getErrors()
    {
        return $this->errors;
    }

}
