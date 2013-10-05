<?php

namespace Flexy\SystemBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\NoResultException;

use Flexy\SystemBundle\Lib\BaseEntity;

/**
 * Doctrine Event Listener
 */
class DoctrineEventListener
{
    /**
     * Pre Persist
     *
     * @param LifecycleEventArgs $args Arguments
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof BaseEntity) {

            // Set the value of the ordering field for new entities
            if (!$entity->getId() && method_exists($entity, 'getOrdering')) {
                // Only if an ordering is not set yet
                if (!$entity->getOrdering()) {
                    $repo = $entityManager->getRepository(get_class($entity));
                    $query = $repo->createQueryBuilder('o')
                        ->orderBy('o.ordering', 'DESC')
                        ->setMaxResults(1)
                        ->getQuery();

                    try {
                        $maxOrderingEntity = $query->getSingleResult();
                        $nextOrdering = $maxOrderingEntity->getOrdering() + 1;
                    } catch (NoResultException $e) {
                        $nextOrdering = 1;
                    }

                    $entity->setOrdering($nextOrdering);
                }
            }
        }
    }
}
