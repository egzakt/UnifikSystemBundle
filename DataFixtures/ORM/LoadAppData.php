<?php

namespace Unifik\Frontend\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

use Unifik\SystemBundle\Entity\App;
use Unifik\SystemBundle\Entity\AppTranslation;
use Unifik\SystemBundle\Entity\AppRepository;

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
        $metadata = $manager->getClassMetaData('Unifik\\SystemBundle\\Entity\\App');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $appBackend = new App();
        $appBackend->setId(AppRepository::BACKEND_APP_ID);
        $appBackend->setCode('backend');
        $appBackend->setOrdering(1);
        $appBackendTranslation = new AppTranslation();
        $appBackendTranslation->setLocale('fr');
        $appBackendTranslation->setName('Backend');
        $appBackendTranslation->setSlug('admin');
        $appBackend->addTranslation($appBackendTranslation);
        $appBackendTranslation = new AppTranslation();
        $appBackendTranslation->setLocale('en');
        $appBackendTranslation->setName('Backend');
        $appBackendTranslation->setSlug('admin');
        $appBackend->addTranslation($appBackendTranslation);

        $manager->persist($appBackend);

        $appFrontend = new App();
        $appFrontend->setId(AppRepository::FRONTEND_APP_ID);
        $appFrontend->setOrdering(2);
        $appFrontendTranslation = new AppTranslation();
        $appFrontendTranslation->setLocale('fr');
        $appFrontendTranslation->setName('Frontend');
        $appFrontendTranslation->setSlug('');
        $appFrontend->addTranslation($appFrontendTranslation);
        $appFrontendTranslation = new AppTranslation();
        $appFrontendTranslation->setLocale('en');
        $appFrontendTranslation->setName('Frontend');
        $appFrontendTranslation->setSlug('');
        $appFrontend->addTranslation($appFrontendTranslation);

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
