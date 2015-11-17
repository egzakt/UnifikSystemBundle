<?php

namespace Unifik\SystemBundle\Entity;

use Unifik\DoctrineBehaviorsBundle\Model as UnifikORMBehaviors;

use Unifik\SystemBundle\Lib\BaseEntityRepository;

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

    /**
     * Select all albums that have photos
     *
     * @param $appId
     *
     * @return mixed
     */
    public function findOneByCodeAndApp($code, $appId = 2)
    {
        $query = $this->createQueryBuilder('n');

        $query->select('n', 'sn', 's', 'st')
            ->leftJoin('n.sectionNavigations', 'sn')
            ->leftJoin('sn.section', 's', 'WITH', $query->expr()->eq('s.app', ':appId'))
            ->leftJoin('s.translations', 'st')
            ->where('n.code = :code')
            ->andWhere('n.app = :appId')
            ->setParameter('code', $code)
            ->setParameter('appId', $appId)
            ->orderBy('n.id', 'ASC')
            ->addOrderBy('sn.ordering', 'ASC')
        ;

        return $this->processQuery($query, true);
    }
}
