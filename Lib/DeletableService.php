<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Egzakt\SystemBundle\Lib\BaseEntity;

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
     * @param  BaseEntity        $entity
     * @param  bool              $requestCheck
     * @return DeletableResponse
     */
    public function delete(BaseEntity $entity, $requestCheck = false)
    {
        $repository = $this->getRepository(get_class($entity));

        if ($requestCheck) {
            if ($this->isDeletable($entity)) {
                $output = $this->fail($this->getErrors());
            } else {
                $output = $this->success('Entity can be deleted.');
            }

            return $output;
        }

        if ($this->isDeletable($entity)) {
            $repository->deleteAndFlush($entity);

            return $this->success('Entity has been deleted.');
        }

        return $this->fail($this->getErrors());

    }

    /**
     * @param  BaseEntity $entity
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
     * @param $errors
     * @return DeletableResponse
     */
    protected function fail($errors)
    {
        return new DeletableResult(DeletableResponse::STATUS_FAIL, 'Entity can\'t be deleted.', $errors);
    }

    /**
     * @param $message
     * @return DeletableResponse
     */
    protected function success($message)
    {
        return new DeletableResult(DeletableResponse::STATUS_SUCCESS, $message);
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
