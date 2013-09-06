<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseEntityRepository;

/**
 * NavigationRepository
 */
class NavigationRepository extends BaseEntityRepository
{
    const SECTION_BAR_ID = 1;
    const SECTION_MODULE_BAR_ID = 2;
    const GLOBAL_MODULE_BAR_ID = 3;
    const APP_MODULE_BAR_ID = 4;

    /**
     * Select all albums that have photos
     *
     * @param $appId
     *
     * @return mixed
     */
    public function findHaveSections($appId = null)
    {
        $query = $this->createQueryBuilder('n')
            ->select('n', 'sn', 's', 'st')
            ->innerJoin('n.sectionNavigations', 'sn')
            ->innerJoin('sn.section', 's')
            ->leftJoin('s.translations', 'st')
            ->orderBy('n.id', 'ASC')
            ->addOrderBy('sn.ordering', 'ASC');

        if ($appId) {
            $query->where('n.app = :appId');
            $query->setParameter('appId', $appId);
        }

        return $this->processQuery($query);
    }
}
