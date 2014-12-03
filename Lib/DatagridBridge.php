<?php

namespace Unifik\SystemBundle\Lib;

use APY\DataGridBundle\Grid\Source\Entity as DatagridEntity;
use Doctrine\ORM\QueryBuilder;

class DatagridBridge
{
    /**
     * @var Core
     */
    private $systemCore;

    public function __construct($systemCore)
    {
        $this->systemCore = $systemCore;
    }

    /**
     * @param DatagridEntity $source
     * @param Callable $callback
     *
     * @return DatagridEntity
     */
    public function addTranslationSupport(DatagridEntity $source, $callback = null)
    {
        $locale = $this->systemCore->getApplicationCore()->getEditLocale();

        $source->manipulateQuery(function(QueryBuilder $query) use ($source, $locale, $callback){
            $this->addQueryTranslationSupport($query, $source, $locale);

            if (is_callable($callback)) {
                $callback($query);
            }
        });

        return $source;
    }

    /**
     * @param QueryBuilder $query
     * @param $source
     * @param $locale
     * @param bool $resetJoin
     */
    public function addQueryTranslationSupport(QueryBuilder $query, $source, $locale, $resetJoin = true)
    {
        if ($resetJoin) {
            $query->resetDQLPart('join');
        }

        $query->leftJoin($source->getTableAlias() . '.translations', '_translations', 'WITH', $query->expr()->orX(
            $query->expr()->eq('_translations.locale', ':locale'),
            $query->expr()->isNull('_translations.id')
        ));

        $query->setParameter('locale', $locale);
    }
}