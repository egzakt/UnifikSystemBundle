<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;

interface DeletableListenerInterface
{
    /**
     * @param  Object  $entity
     * @return boolean
     */
    public function isDeletable($entity);

    /**
     * @return ArrayCollection
     */
    public function getErrors();

}
