<?php

namespace Egzakt\Frontend\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

use Egzakt\SystemBundle\Entity\App;

class LoadAppData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Load
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('Egzakt\\SystemBundle\\Entity\\App');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $appBackend = new App();
        $appBackend->setId(1);
        $appBackend->setName('Backend');
        $appBackend->setPrefix('admin');
        $appBackend->setOrdering(1);

        $manager->persist($appBackend);

        $appFrontend = new App();
        $appFrontend->setId(2);
        $appFrontend->setName('Frontend');
        $appFrontend->setOrdering(2);

        $manager->persist($appFrontend);

        $manager->flush();

        $this->addReference('app-backend', $appBackend);
        $this->addReference('app-frontend', $appFrontend);
    }

    /**
     * Get Order
     *
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}