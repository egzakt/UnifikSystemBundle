<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseEntityRepository;

/**
 * BundleRepository
 */
class BundleRepository extends BaseEntityRepository
{
    /**
     * Find a bundle using its params
     *
     * @param string $name  The name of the param
     * @param bool   $value The value of the param
     *
     * @return array
     */
    public function findByParam($name, $value = false)
    {
        $qry = $this->createQueryBuilder('b')
            ->innerJoin('b.params', 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name);

        if ($value) {
            $qry->andWhere('p.value = :value')
                ->setParameter('value', $value);
        }

        $qry->orderBy('p.id', 'ASC');

        return $qry->getQuery()->getResult();
    }
}