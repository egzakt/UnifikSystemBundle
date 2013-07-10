<?php

namespace Egzakt\Frontend\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Egzakt\SystemBundle\Entity\Navigation;

class LoadNavigationData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $sectionBar = new Navigation();
        $sectionBar->setCode('_section_bar');
        $sectionBar->setName('Sections bar');
        $sectionBar->setApp($manager->merge($this->getReference('app-backend')));

        $sectionModuleBar = new Navigation();
        $sectionModuleBar->setCode('_section_module_bar');
        $sectionModuleBar->setName('Section modules');
        $sectionModuleBar->setApp($manager->merge($this->getReference('app-backend')));

        $globalModuleBar = new Navigation();
        $globalModuleBar->setCode('_global_module_bar');
        $globalModuleBar->setName('Global modules');
        $globalModuleBar->setApp($manager->merge($this->getReference('app-backend')));

        $globalApp = new Navigation();
        $globalApp->setCode('_app_module_bar');
        $globalApp->setName('Application modules');
        $globalApp->setApp($manager->merge($this->getReference('app-backend')));

        $manager->persist($sectionBar);
        $manager->persist($sectionModuleBar);
        $manager->persist($globalModuleBar);
        $manager->persist($globalApp);

        $this->addReference('navigation-section-bar', $sectionBar);
        $this->addReference('navigation-section-modules-bar', $sectionModuleBar);
        $this->addReference('navigation-global-module-bar', $globalModuleBar);
        $this->addReference('navigation-app-module-bar', $globalApp);

        // Frontend navigations
        $primary = new Navigation();
        $primary->setCode('primary');
        $primary->setName('Primary');
        $primary->setApp($manager->merge($this->getReference('app-frontend')));

        $secondary = new Navigation();
        $secondary->setCode('secondary');
        $secondary->setName('Secondary');
        $secondary->setApp($manager->merge($this->getReference('app-frontend')));

        $footer = new Navigation();
        $footer->setCode('footer');
        $footer->setName('Footer');
        $footer->setApp($manager->merge($this->getReference('app-frontend')));

        $manager->persist($primary);
        $manager->persist($secondary);
        $manager->persist($footer);

        $this->addReference('navigation-frontend-primary', $primary);
        $this->addReference('navigation-frontend-secondary', $secondary);
        $this->addReference('navigation-frontend-footer', $footer);

        $manager->flush();
    }

    /**
     * Get Order
     *
     * @return int
     */
    public function getOrder()
    {
        return 4;
    }
}