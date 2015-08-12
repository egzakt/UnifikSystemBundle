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
        $qb->select('a, at, s, st, m, c, ct, cc, cct')
            ->innerJoin('a.translations', 'at')
            ->innerJoin('a.sections', 's')
            ->innerJoin('s.mappings', 'm')
            ->innerJoin('s.translations', 'st')
            ->innerJoin('s.sectionNavigations', 'sn')
            ->innerJoin('sn.navigation', 'n')
            ->leftJoin('s.children', 'c')
            ->leftJoin('c.translations', 'ct', 'WITH', 'ct.active = true AND ct.locale = :locale')
            ->leftJoin('c.children', 'cc')
            ->leftJoin('cc.translations', 'cct', 'WITH', 'cct.active = true AND cct.locale = :locale')
            ->andWhere('m.app = a.id')
            ->andWhere('n.app = a.id')
            ->andWhere('m.type = :mapType')
            ->andWhere('st.active = true')
            ->andWhere('st.locale = :locale')
            ->andWhere('at.active = true')
            ->andWhere('at.locale = :locale')
            ->andWhere('n.code = :code')
            ->setParameter('mapType', 'route')
            ->setParameter('locale', $this->getLocale())
            ->setParameter('code', $code)
            ->addOrderBy('sn.ordering', 'ASC')
            ->addOrderBy('m.ordering', 'ASC')
        ;

        $this->setReturnQueryBuilder($returnQb);
        return $this->processQuery($qb);
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
