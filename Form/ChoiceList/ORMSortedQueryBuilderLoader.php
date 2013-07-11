<?php

namespace Egzakt\SystemBundle\Form\ChoiceList;

use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use Egzakt\SystemBundle\Lib\TreeEntityOrderer;

/**
 * Class ORMSortedQueryBuilderLoader
 */
class ORMSortedQueryBuilderLoader extends ORMQueryBuilderLoader {

    /**
     * Contains the query builder that builds the query for fetching the
     * entities
     *
     * This property should only be accessed through queryBuilder.
     *
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var TreeEntityOrderer
     */
    protected $treeEntityOrderer;

    /**
     * @var bool
     */
    protected $automaticSorting;

    /**
     * Construct an ORM Query Builder Loader
     *
     * @param QueryBuilder|\Closure $queryBuilder
     * @param EntityManager         $manager
     * @param string                $class
     * @param bool $automaticSorting
     * @param TreeEntityOrderer $treeEntityOrderer
     *
     * @throws UnexpectedTypeException
     */
    public function __construct($queryBuilder, $manager = null, $class = null, $automaticSorting = null, TreeEntityOrderer $treeEntityOrderer)
    {
        // If a query builder was passed, it must be a closure or QueryBuilder
        // instance
        if (!($queryBuilder instanceof QueryBuilder || $queryBuilder instanceof \Closure)) {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
        }

        if ($queryBuilder instanceof \Closure) {
            $queryBuilder = $queryBuilder($manager->getRepository($class));

            if (!$queryBuilder instanceof QueryBuilder) {
                throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder');
            }
        }

        $this->queryBuilder = $queryBuilder;

        $this->treeEntityOrderer = $treeEntityOrderer;
        $this->automaticSorting = $automaticSorting;
    }

    /**
     * {@inheritDoc}
     *
     * This function overwrites the original function to add the "automaticSorting" functionnality
     */
    public function getEntities()
    {
        $entities = $this->queryBuilder->getQuery()->execute();

        // If automaticSorting is On
        if ($this->automaticSorting) {
            // Sort the entities
            $entities = $this->treeEntityOrderer->sortEntities($entities);
        }

        return $entities;
    }

}