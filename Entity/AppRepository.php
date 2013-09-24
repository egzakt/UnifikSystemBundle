<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseEntityRepository;

/**
 * App repository
 */
class AppRepository extends BaseEntityRepository
{
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
