<?php

namespace Unifik\SystemBundle\Entity;

use Unifik\DoctrineBehaviorsBundle\Model as UnifikORMBehaviors;

use Unifik\SystemBundle\Lib\BaseEntityRepository;

/**
 * NavigationRepository
 */
class NavigationRepository extends BaseEntityRepository
{
    use UnifikORMBehaviors\Repository\TranslatableEntityRepository;

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
        $qb = $this->createQueryBuilder('n');

        $qb->select('n')
            ->where('n.code = :code')
            ->andWhere('n.app = :appId')
            ->setParameter('code', $code)
            ->setParameter('appId', $appId)
            ->orderBy('n.id', 'ASC')
            ;

        $qb2 = clone $qb;
        $nav = $qb2->getQuery()->getOneOrNullResult();

        $qb2 = clone $qb;
        $qb2->select('PARTIAL n.{id}', 'sn', 's', 'st')
            ->innerJoin('n.sectionNavigations', 'sn')
            ->innerJoin('sn.section', 's', 'WITH', 's.app = :appId')
            ->innerJoin('s.translations', 'st', 'WITH', 'st.active = true AND st.locale = :locale')
            ->orderBy('n.id', 'ASC')
            ->addOrderBy('sn.ordering', 'ASC')
            ->setParameter('locale', $this->getLocale())
        ;
        $qb2->getQuery()->getResult();

        $qb2 = clone $qb;
        $qb2->select('PARTIAL n.{id}', 'sn', 's', 'c', 'ct')
            ->innerJoin('n.sectionNavigations', 'sn')
            ->innerJoin('sn.section', 's', 'WITH', 's.app = :appId')
            ->innerJoin('s.translations', 'st', 'WITH', 'st.active = true AND st.locale = :locale')
            ->innerJoin('s.children', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.active = true AND ct.locale = :locale')
            ->orderBy('n.id', 'ASC')
            ->addOrderBy('sn.ordering', 'ASC')
            ->setParameter('locale', $this->getLocale())
        ;
        $qb2->getQuery()->getResult();

        return $nav;
    }
}
