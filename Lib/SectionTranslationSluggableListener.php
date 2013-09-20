<?php

namespace Egzakt\SystemBundle\Lib;

use Egzakt\DoctrineBehaviorsBundle\ORM\Sluggable\SluggableListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Class SectionTranslationSluggableListener
 */
class SectionTranslationSluggableListener extends SluggableListener
{

    /**
     * Returns the Select QueryBuilder that will check for a similar slug in the table
     * The slug will be valid when the Query returns 0 rows.
     *
     * @param string $slug
     * @param mixed $entity
     * @param EntityManager $em
     *
     * @return QueryBuilder
     */
    public function getSelectQueryBuilder($slug, $entity, EntityManager $em)
    {
        $translatable = $entity->getTranslatable();

        $queryBuilder = $em->createQueryBuilder()
                ->select('DISTINCT(s.slug)')
                ->from('Egzakt\SystemBundle\Entity\SectionTranslation', 's')
                ->innerJoin('s.translatable', 't')
                ->where('s.slug = :slug')
                ->andWhere('s.locale = :locale')
                ->setParameters([
                        'slug' => $slug,
                        'locale' => $entity->getLocale()
                ]);

        // On update, look for other slug, not the current entity slug
        if ($em->getUnitOfWork()->isScheduledForUpdate($entity)) {
            $queryBuilder->andWhere('t.id <> :id')
                ->setParameter('id', $translatable->getId());
        }

        // Only look for slug on the same level
        if ($translatable->getParent()) {
            $queryBuilder->andWhere('t.parent = :parent')
                ->setParameter('parent', $translatable->getParent());
        }

        return $queryBuilder;
    }

}