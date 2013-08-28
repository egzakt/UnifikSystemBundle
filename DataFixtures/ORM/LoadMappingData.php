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
        // Home section defaut route
        $mapping = new Mapping();
        $mapping->setSection($manager->merge($this->getReference('section-home')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setTarget('egzakt_system_backend_text');
        $mapping->setType('route');

        $manager->persist($mapping);

        // Home section texts module
        $mapping = new Mapping();
        $mapping->setSection($manager->merge($this->getReference('section-home')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setNavigation($manager->merge($this->getReference('navigation-section-modules-bar')));
        $mapping->setTarget('EgzaktSystemBundle:Backend/Text/Navigation:SectionModuleBar');
        $mapping->setType('render');

        $manager->persist($mapping);

        // Global modules bar
        $mapping = new Mapping();
        $mapping->setNavigation($manager->merge($this->getReference('navigation-global-module-bar')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setTarget('EgzaktSystemBundle:Backend/User/Navigation:GlobalModuleBar');
        $mapping->setType('render');

        $manager->persist($mapping);

        $mapping = new Mapping();
        $mapping->setNavigation($manager->merge($this->getReference('navigation-global-module-bar')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setTarget('EgzaktSystemBundle:Backend/Role/Navigation:GlobalModuleBar');
        $mapping->setType('render');

        $manager->persist($mapping);

        $mapping = new Mapping();
        $mapping->setNavigation($manager->merge($this->getReference('navigation-global-module-bar')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setTarget('EgzaktSystemBundle:Backend/Locale/Navigation:GlobalModuleBar');
        $mapping->setType('render');

        $manager->persist($mapping);
        
        // App global bar modules
        $mapping = new Mapping();
        $mapping->setNavigation($manager->merge($this->getReference('navigation-app-module-bar')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setTarget('EgzaktSystemBundle:Backend/Section/Navigation:AppModuleBar');
        $mapping->setType('render');

        $manager->persist($mapping);

        $manager->flush();

        $this->loadFrontend($manager);
    }

    public function loadFrontend(ObjectManager $manager)
    {
        $mapping = new Mapping();
        $mapping->setTarget('egzakt_system_frontend_home');
        $mapping->setType('route');
        $mapping->setSection($manager->merge($this->getReference('section-home')));
        $mapping->setApp($manager->merge($this->getReference('app-frontend')));
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