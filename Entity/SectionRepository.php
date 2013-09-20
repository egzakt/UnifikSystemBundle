<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\DoctrineBehaviorsBundle\Model as EgzaktORMBehaviors;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * SectionRepository
 */
class SectionRepository extends EntityRepository implements ContainerAwareInterface
{
    use EgzaktORMBehaviors\Repository\TranslatableEntityRepository;

    /**
     * Find By Navigation From Tree
     *
     * @param string     $navigationName Navigation name
     * @param array|null $criteria       Criteria
     * @param array|null $orderBy        OrderBy fields
     *
     * @return array
     */
    public function findByNavigationFromTree($navigationName, array $criteria = null, array $orderBy = null)
    {
        $tree = $this->findAllFromTree($criteria, $orderBy);

        $navigationSections = array();
        foreach ($tree as $key => $section) {

            foreach ($section->getSectionNavigations() as $sectionNavigation) {

                if ($sectionNavigation->getNavigation()->getName() == $navigationName) {
                    $navigationSections[$sectionNavigation->getOrdering()] = $section;
                }
            }

        }

        ksort($navigationSections);

        return $navigationSections;
    }

    /**
     * Find All From Tree
     *
     * @param array|null $criteria Criteria
     * @param array|null $orderBy  OrderBy fields
     *
     * @return array
     */
    public function findAllFromTree(array $criteria = null, array $orderBy = null)
    {
        $dql = 'SELECT s, t, sn, n, b, p
                FROM EgzaktSystemBundle:Section s
                LEFT JOIN s.sectionNavigations sn
                LEFT JOIN sn.navigation n
                LEFT JOIN sb.bundle b
                LEFT JOIN b.params p ';

        if ($this->getCurrentAppName() == 'backend') {
            $dql .= 'LEFT JOIN s.translations t ';
        } else {
            $dql .= 'INNER JOIN s.translations t ';
            $criteria['locale'] = $this->getLocale();
            if ($this->_em->getClassMetadata($this->_entityName . 'Translation')->hasField('active') && !in_array('active', array_keys($criteria))) {
                $criteria['active'] = true;
            }
        }

        if ($criteria) {

            $dql .= 'WHERE ';

            foreach (array_keys($criteria) as $column) {
                if (!$this->_class->hasField($column) && $this->_em->getClassMetadata($this->_entityName . 'Translation')->hasField($column)) {
                    $dql .= 't.' . $column . ' = :' .  $column . ' AND ';
                } else {
                    $dql .= 's.' . $column . ' = :' .  $column . ' AND ';
                }
            }

            $dql = substr($dql, 0, -4);
        }

        if ($orderBy) {
            // Temporary hack (waiting for the function to be rewritten)
            if ($this->getCurrentAppName() == 'backend') {
                $dql .= 'ORDER BY s.' . key($orderBy);
            } else {
                // TODO: add an ordering column in the navigation table
                $dql .= 'ORDER BY n.id, sn.' . key($orderBy);
            }

            $dql .= ' ' . $orderBy['ordering'];
        }

        $query = $this->getEntityManager()->createQuery($dql);

        if ($criteria) {
            $query->setParameters($criteria);
        }

        $tree = $this->buildTree($query->getResult());

        return $tree;
    }

    /**
     * Build Tree
     *
     * @param array $sections Sections
     *
     * @return array
     */
    private function buildTree($sections)
    {
        $tree = array();

        foreach ($sections as $section) {

            $section->setChildren(null);
            $tree[$section->getId()] = $section;
        }

        foreach ($tree as $section) {

            if ($parent = $section->getParent()) {
                if (isset($tree[$parent->getId()])) {
                    $tree[$parent->getId()]->addChildren($section);
                }
            }
        }

        foreach ($tree as $sectionId => $section) {

            if ($section->getParent()) {
                unset($tree[$sectionId]);
            }
        }

        return $tree;
    }

    /**
     * Find By Navigation and App
     *
     * @param integer $navigationId
     * @param integer $appId
     *
     * @return array
     */
    public function findByNavigationAndApp($navigationId, $appId)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s', 'st')
            ->innerJoin('s.sectionNavigations', 'sn')
            ->where('s.app = :appId')
            ->andWhere('sn.navigation = :navigationId')
            ->orderBy('sn.ordering')

            ->setParameter('appId', $appId)
            ->setParameter('navigationId', $navigationId);

        if ($this->getCurrentAppName() != 'backend') {
            $queryBuilder->innerJoin('s.translations', 'st')
                ->andWhere('st.active = true')
                ->andWhere('st.locale = :locale')
                ->setParameter('locale', $this->getLocale());
        }

        return $this->processQuery($queryBuilder);
    }

    public function findByAppJoinChildren($appId)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s', 'st', 'sm', 'c', 'ct', 'cm')
            ->leftJoin('s.translations', 'st')
            ->leftJoin('s.mappings', 'sm')
            ->leftJoin('s.children', 'c')
            ->leftJoin('c.translations', 'ct')
            ->leftJoin('c.mappings', 'cm')
            ->where('s.app = :appId')
            ->orderBy('s.ordering')
            ->setParameter('appId', $appId->getId());

        return $this->processQuery($queryBuilder);
    }

    public function findRootsWithoutNavigation($appId = null)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s', 'st', 'c', 'ct')
            ->leftJoin('s.translations', 'st')
            ->leftJoin('s.sectionNavigations', 'n')
            ->leftJoin('s.children', 'c')
            ->leftJoin('c.translations', 'ct')
            ->where('s.parent IS NULL')
            ->andWhere('n.id IS NULL')
            ->orderBy('s.ordering');

        if ($appId) {
            $queryBuilder->andWhere('s.app = :appId');
            $queryBuilder->setParameter('appId', $appId);
        }

        return $this->processQuery($queryBuilder);
    }

    /**
     * Find Having Roles
     *
     * @param $roles
     *
     * @return mixed
     */
    public function findHavingRoles($roles)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s.id')
            ->innerJoin('s.roles', 'r')
            ->where('r.role IN (:roles)')
            ->setParameter('roles', $roles);

        // Array of array('id' => 1)
        $results = $queryBuilder->getQuery()->getScalarResult();

        $ids = array();
        foreach ($results as $section) {
            $ids[] = $section['id'];
        }

        // Return the list of IDs in a single array
        return $ids;
    }
}
