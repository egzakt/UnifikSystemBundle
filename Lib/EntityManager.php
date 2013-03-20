<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager as BaseEntityManager;
use Symfony\Component\DependencyInjection\Container;

use Egzakt\SystemBundle\Lib\BaseEntityRepository;

/**
 * The EntityManager is the central access point to ORM functionality.
 *
 */
class EntityManager extends BaseEntityManager
{
    /**
     * @var Container
     */
    private $container;

    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        if ( ! $config->getMetadataDriverImpl()) {
            throw ORMException::missingMappingDriverImpl();
        }

        switch (true) {
            case (is_array($conn)):
                $conn = \Doctrine\DBAL\DriverManager::getConnection(
                    $conn, $config, ($eventManager ?: new EventManager())
                );
                break;

            case ($conn instanceof Connection):
                if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
                    throw ORMException::mismatchedEventManager();
                }
                break;

            default:
                throw new \InvalidArgumentException("Invalid argument: " . $conn);
        }

        $x = new EntityManager($conn, $config, $conn->getEventManager());

        return $x;
    }

    /**
     * Gets the repository and inject the container on every Egzakt repository
     *
     * @param string $entityName The name of the entity.
     *
     * @return EntityRepository The repository class.
     */
    public function getRepository($entityName)
    {
        $repository = parent::getRepository($entityName);

        if ($repository instanceof BaseEntityRepository) {
            $repository->setContainer($this->container);
        }

        return $repository;
    }

    /**
     * Set container
     *
     * @param Container $container The container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}
