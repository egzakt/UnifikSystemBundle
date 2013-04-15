<?php

namespace Egzakt\Frontend\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Egzakt\SystemBundle\Entity\Mapping;

class LoadMappingData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $mapping = new Mapping();
        $mapping->setSection($manager->merge($this->getReference('section-home')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setTarget('egzakt_system_backend_text');
        $mapping->setType('route');

        $manager->persist($mapping);

        $mapping = new Mapping();
        $mapping->setSection($manager->merge($this->getReference('section-home')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setNavigation($manager->merge($this->getReference('navigation-section-bar')));
        $mapping->setTarget('EgzaktSystemBundle:Backend/Text/Navigation:BundleBar');
        $mapping->setType('render');

        $manager->persist($mapping);

        // Global module bar
        $mapping = new Mapping();
        $mapping->setNavigation($manager->merge($this->getReference('navigation-global-module-bar')));
        $mapping->setTarget('EgzaktSystemBundle:Backend/User/Navigation:GlobalModuleBar');
        $mapping->setType('render');

        $manager->persist($mapping);

        $mapping = new Mapping();
        $mapping->setNavigation($manager->merge($this->getReference('navigation-global-module-bar')));
        $mapping->setTarget('EgzaktSystemBundle:Backend/Section/Navigation:GlobalModuleBar');
        $mapping->setType('render');

        $manager->persist($mapping);

        $manager->flush();
    }

    /**
     * Get Order
     *
     * @return int
     */
    public function getOrder()
    {
        return 5;
    }
}