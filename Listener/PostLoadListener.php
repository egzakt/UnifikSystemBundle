<?php

namespace Flexy\SystemBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Container;

use Flexy\SystemBundle\Lib\BaseEntity;

/**
 * Post Load Listener
 */
class PostLoadListener
{
    private $container;

    /**
     * Post Load
     *
     * @param LifecycleEventArgs $args Arguments
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        /** @var BaseEntity $entity */
        $entity = $args->getEntity();

        if ($entity instanceof BaseEntity) {
            $entity->setContainer($this->container);
        }
    }

    /**
     * Set Container
     *
     * @param Container $container The Container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}
