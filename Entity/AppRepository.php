<?php

namespace Unifik\SystemBundle\Entity;

use Unifik\SystemBundle\Lib\BaseEntityRepository;
use Unifik\DoctrineBehaviorsBundle\Model as UnifikORMBehaviors;
use Doctrine\ORM\NoResultException;

/**
 * App repository
 */
class AppRepository extends BaseEntityRepository
{
    use UnifikORMBehaviors\Repository\TranslatableEntityRepository;

    const BACKEND_APP_ID = 1;
    const FRONTEND_APP_ID = 2;

    public function findOneByCode($code)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a', 'at')
            ->leftJoin('a.translations', 'at')
            ->andWhere('a.code = :code')
            ->setParameter('code', $code)
        ;

        $result = $qb->getQuery()->getResult();
        if (count($result) > 0)
            return $result[0];
        return null;
    }

    public function findFirstOneExcept($exceptId)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a', 'at')
            ->leftJoin('a.translations', 'at')
            ->andWhere('a.id <> :exceptId')
            ->setParameter('exceptId', $exceptId)
            ->orderBy('a.ordering', 'ASC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getResult();
        if (count($result) > 0)
            return $result[0];
        return null;
    }

    public function findAllExcept($exceptIds)
    {
        if (!is_array($exceptIds)) {
            $exceptIds = array($exceptIds);
        }

        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.ordering', 'ASC');
        if ($exceptIds) {
            $qb->andWhere('a.id NOT IN (:exceptId)')
                ->setParameter('exceptId', $exceptIds);
        }

        return $this->processQuery($qb);
    }


    public function findAllForNavigation($code = '', $exceptIds)
    {
        $returnQb = $this->getReturnQueryBuilder();
        $this->setReturnQueryBuilder(true);

        $qb = $this->findAllExcept($exceptIds);
        $qb->select('a', 'at', 's', 'st', 'm', 'sn', 'n')
            ->innerJoin('a.translations', 'at')
            ->andWhere('at.active = true')
            ->andWhere('at.locale = :locale')
            ->innerJoin('a.sections', 's')
            ->innerJoin('s.mappings', 'm', 'WITH', 'm.app = a.id AND m.type = :mapType')
            ->innerJoin('s.translations', 'st', 'WITH', 'st.active = true AND st.locale = :locale')
            ->innerJoin('s.sectionNavigations', 'sn')
            ->innerJoin('sn.navigation', 'n', 'WITH', 'n.app = a.id AND n.code = :code')
            ->addOrderBy('sn.ordering', 'ASC')
            ->addOrderBy('s.ordering', 'ASC')
            ->setParameter('locale', $this->getLocale())
            ->setParameter('code', $code)
            ->setParameter('mapType', 'route')
            ;
        $qb2 = clone $qb;
        $apps = $qb2->getQuery()->getResult();

        $qb2 = clone $qb;
        $qb2->select('PARTIAL a.{id}', 's', 'c', 'ct')
            ->innerJoin('s.children', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.active = true AND ct.locale = :locale')
            ->addOrderBy('c.ordering', 'ASC')
        ;
        $qb2->getQuery()->getResult();

        $qb2 = clone $qb;
        $qb2->select('PARTIAL a.{id}', 's', 'c', 'cc', 'cct')
            ->innerJoin('s.children', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.active = true AND ct.locale = :locale')
            ->innerJoin('c.children', 'cc')
            ->innerJoin('cc.translations', 'cct', 'WITH', 'cct.active = true AND cct.locale = :locale')
            ->addOrderBy('c.ordering', 'ASC')
            ->addOrderBy('cc.ordering', 'ASC')
        ;
        $qb2->getQuery()->getResult();

        $this->setReturnQueryBuilder($returnQb);

        return $apps;
    }

    public function findAllHasAccess($securityContext = null, $userId = null)
    {
        $qb = $this->createQueryBuilder('a');
        if ($securityContext !== null && !$securityContext->isGranted('ROLE_BACKEND_ADMIN')) {
            $qb->select('a', 'at', 's')
                ->leftJoin('a.translations', 'at')
                ->innerJoin('a.sections', 's')
                ->innerJoin('s.roles', 'sr')
                ->innerJoin('sr.users', 'ru')
                ->where('ru.id = :userId')
                ->setParameter('userId', $userId);
        }
        $qb->orderBy('a.ordering', 'ASC');
        return $this->processQuery($qb);
    }

    /**
     * Find the last update of an App entity
     *
     * @param null $queryBuilder
     * @return mixed
     */
    public function findLastUpdate($queryBuilder = null)
    {
        if (!$queryBuilder) {
            $queryBuilder = $this->createQueryBuilder('a');
        }

        try {
            return $queryBuilder->select('a.updatedAt')
                ->addOrderBy('a.updatedAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            return null;
        }
    }
}
