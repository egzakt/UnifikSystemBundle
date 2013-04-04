<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Egzakt\SystemBundle\Lib\BaseEntityRepository;

/**
 * SectionRepository
 */
class SectionRepository extends BaseEntityRepository
{
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
        $dql = 'SELECT s, t, sb, sn, n, b, p
                FROM EgzaktSystemBundle:Section s
                LEFT JOIN s.sectionBundles sb
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

    /**
     * Returns all sections with their subsections, sorted
     *
     * @param integer $maxLevel
     *
     * @return mixed
     */
    public function findAllWithSubsections($maxLevel)
    {

        /** @var int $nivMax */
        $nivMax = $maxLevel - 1;

        // Construire le CASE ... WHEN ... THEN ...
        $case = " CASE ";
        for ($i = 1; $i <= $nivMax; $i++) {
            $when = array();
            for ($j = $i+1; $j <= $nivMax+1; $j++) {
                $when[] = "sn$j.id IS NULL";
            }
            $then = array();
            for ($j = $i; $j > 0; $j--) {
                $elem = "";
                if ($j == $i) {
                    $elem = "sn$j.navigation_id *" . (pow(100, $nivMax) * 10) . " + ";
                }
                $then[] = $elem . "s$j.ordering*" . pow(100, $nivMax - $i + $j);
            }
            $case .= " WHEN " . implode(" AND ", $when);
            $case .= " THEN " . implode("  +  ", $then);
        }
        $else = array();
        for ($i = $nivMax; $i >= 0; $i--) {
            $else[] = "s" . ($i+1) . ".ordering*" . (pow(100, $i));
        }
        $case .= " ELSE ";
        $case .= 'sn' . ($nivMax+1) . ".navigation_id*" . (pow(100, $nivMax) * 10) . " + " . implode(' + ', $else);
        $case .= " END as master_ordering";

        $order = "master_ordering";

        // Construire les clauses FROM et WHERE
        $from = "";
        $aWhere = array();
        for ($i = 1; $i <= $nivMax+1; $i++) {
            $from .= ($i == 1 ? " FROM Section s$i" : " LEFT JOIN Section s$i ON s".($i - 1).".parent_id = s$i.id ") .
                " LEFT JOIN SectionNavigation sn$i ON s$i.id = sn$i.section_id" .
                " LEFT JOIN SectionTranslation st$i ON s$i.id = st$i.translatable_id";
            $aWhere[] = " ((st$i.locale = '" . $this->getLocale() . "' AND st$i.active = 1) OR s$i.id IS NULL) ";
        }
        $where = " WHERE " . implode(' AND ', $aWhere);

        $rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('Egzakt\Backend\SectionBundle\Entity\Section', 's');

        $sql = "SELECT s1.*, $case $from $where ORDER BY $order";

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        $return = array();
        /** @var Section $section */
        foreach ($results as $section) {
            $return[$section->getId()] = $section->getHierarchicalName();
        }

        return $return;

    }
}