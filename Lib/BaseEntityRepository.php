<?php

namespace Flexy\SystemBundle\Lib;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityRepository;

/**
 * Flexy Backend Base for Entities
 */
abstract class BaseEntityRepository extends EntityRepository implements ContainerAwareInterface
{
    /**
     * Dependency injection container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var boolean $returnQueryBuilder
     */
    private $returnQueryBuilder;

    /**
     * Sets the Container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Set ReturnQueryBuilder
     *
     * @param bool $returnQueryBuilder
     */
    public function setReturnQueryBuilder($returnQueryBuilder)
    {
        $this->returnQueryBuilder = $returnQueryBuilder;
    }

    /**
     * Get ReturnQueryBuilder
     *
     * @return bool
     */
    public function getReturnQueryBuilder()
    {
        return $this->returnQueryBuilder;
    }

    /**
     * Returns the Query Builder or the results depending on the repository parameters
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param bool                       $singleResult
     *
     * @return mixed
     */
    protected function processQuery($queryBuilder, $singleResult = false)
    {
        if ($this->returnQueryBuilder) {
            return $queryBuilder;
        }

        if ($singleResult) {
            return $queryBuilder->getQuery()->getSingleResult();
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
