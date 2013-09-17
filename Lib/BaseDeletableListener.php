<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;

abstract class BaseDeletableListener implements DeletableListenerInterface
{

    /**
     * @var ArrayCollection
     */
    private $errors;

    public function __construct()
    {
        $this->errors = new ArrayCollection();
    }

    /**
     * @param $message
     */
    protected function addError($message)
    {
        if (null === $this->errors) {
            $this->errors = new ArrayCollection();
        }
        $this->errors->add($message);
    }

    /**
     * @inheritdoc
     */
    abstract public function isDeletable($entity);

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        return $this->getErrors()->isEmpty();
    }

}
