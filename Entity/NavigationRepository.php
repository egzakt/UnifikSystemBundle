<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseEntityRepository;

/**
 * NavigationRepository
 */
class NavigationRepository extends BaseEntityRepository
{
    /**
     * Select all albums that have photos
     *
     * @return mixed
     */
    public function findHaveSections()
    {
        $query = $this->createQueryBuilder('n')
            ->select('n', 'sn', 's', 'st')
            ->innerJoin('n.sectionNavigations', 'sn')
            ->innerJoin('sn.section', 's')
            ->leftJoin('s.translations', 'st')
            ->orderBy('n.id', 'ASC')
            ->addOrderBy('sn.ordering', 'ASC');

        return $this->processQuery($query);
    }
}