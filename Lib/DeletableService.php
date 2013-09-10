<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

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

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
        $this->listeners = new ArrayCollection();
        $this->errors = new ArrayCollection();
    }

    /**
     * Start a delete action for the entity passed in parameter.
     * You can perform a check-only by passing "true" to the second parameter.
     *
     * First, the method check if the entity can be deleted by going through the listeners.
     * If a single listener fail, then the entity can't be deleted.
     *
     * Return a result in an object form containing the status ( fail/success ).
     *
     * @param  Object            $entity
     * @param  bool              $requestCheck
     * @return DeletableResult
     */
    public function delete($entity, $requestCheck = false)
    {
        $repository = $this->getRepository(get_class($entity));

        if ($requestCheck) {
            if ($this->isDeletable($entity)) {
                $output = $this->fail();
            } else {
                $output = $this->successDeletable();
            }

            return $output;
        }

        if ($this->isDeletable($entity)) {
            $repository->deleteAndFlush($entity);

            return $this->successDeleted();
        }

        return $this->fail();

    }

    /**
     * Check if this entity can be deleted or not.
     * We run through a list a listeners and if a listener fail, then the entity is not deletable.
     * Listeners are attached to the service and bound to the entity class name.
     *
     * @param  Object $entity
     * @return bool
     */
    public function isDeletable($entity)
    {
        $classname = get_class($entity);

        if (!$this->getListeners()->containsKey($classname)) {
            return true;
        }

        foreach ($this->getListeners()->get($classname) as $listener) {
            if (!$listener->isDeletable($entity)) {
                $this->setErrors($listener->getErrors());

                return false;
            }
        }

        return true;
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
    protected function fail()
    {
        return new DeletableResult(DeletableResult::STATUS_FAIL, 'Entity can\'t be deleted.', $this->getErrors());
    }

    /**
     * Return a DeletableResult with a Deletable status.
     *
     * @return DeletableResult
     */
    protected function successDeletable()
    {
        return new DeletableResult(DeletableResult::STATUS_DELETABLE, 'Entity can be deleted.');
    }


    /**
     * Return a DeletableResult with a Deleted status.
     *
     * @return DeletableResult
     */
    protected function successDeleted()
    {
        return new DeletableResult(DeletableResult::STATUS_DELETED, 'Entity has been deleted.');
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

    /**
     * @param $classname
     * @return EntityRepository
     */
    protected function getRepository($classname)
    {
        return $this->entityManager->getRepository($classname);
    }

}
