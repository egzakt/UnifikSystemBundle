<?php

namespace Flexy\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;

interface DeletableListenerInterface
{
    /**
     * Check if this entity can be deleted.
     * @param  Object  $entity
     * @return bool
     */
    public function isDeletable($entity);

    /**
     * @return ArrayCollection
     */
    public function getErrors();

}
