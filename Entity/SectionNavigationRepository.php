<?php

namespace Unifik\SystemBundle\Entity;

use Unifik\SystemBundle\Lib\BaseEntityRepository;

/**
 * SectionNavigation Repository
 */
class SectionNavigationRepository extends BaseEntityRepository
{
    /**
     * Find the last update of a Section entity
     *
     * @param null $queryBuilder
     * @return mixed
     */
    public function findLastUpdate($queryBuilder = null)
    {
        if (!$queryBuilder) {
            $queryBuilder = $this->createQueryBuilder('sn');
        }

        return $queryBuilder->select('sn.updatedAt')
            ->addOrderBy('sn.updatedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getSingleScalarResult();
    }
}
