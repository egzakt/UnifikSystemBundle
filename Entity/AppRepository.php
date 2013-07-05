<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseEntityRepository;

/**
 * App repository
 */
class AppRepository extends BaseEntityRepository
{
    public function findFirstOneExcept($exceptName)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.name <> :except')
            ->setParameter('except', $exceptName)
            ->orderBy('a.ordering', 'ASC')
            ->setMaxResults(1);

        return $this->processQuery($qb, true);
    }
}