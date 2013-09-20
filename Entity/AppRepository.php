<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\DoctrineBehaviorsBundle\Model as EgzaktORMBehaviors;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * App repository
 */
class AppRepository extends EntityRepository implements ContainerAwareInterface
{
    use EgzaktORMBehaviors\Repository\TranslatableEntityRepository;

    const BACKEND_APP_ID = 1;
    const FRONTEND_APP_ID = 2;

    public function findFirstOneExcept($exceptId)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.id <> :exceptId')
            ->setParameter('exceptId', $exceptId)
            ->orderBy('a.ordering', 'ASC')
            ->setMaxResults(1);

        return $this->processQuery($qb, true);
    }

    public function findAllExcept($exceptId)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.id <> :exceptId')
            ->setParameter('exceptId', $exceptId)
            ->orderBy('a.ordering', 'ASC');

        return $this->processQuery($qb);
    }
}
