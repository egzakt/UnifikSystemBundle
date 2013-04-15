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
        $sectionBar->setName('_section_bar');

        $sectionModuleBar = new Navigation();
        $sectionModuleBar->setName('_section_module_bar');

        $globalModuleBar = new Navigation();
        $globalModuleBar->setName('_global_module_bar');

        $manager->persist($sectionBar);
        $manager->persist($sectionModuleBar);
        $manager->persist($globalModuleBar);
        $manager->flush();

        $this->addReference('navigation-section-bar', $sectionBar);
        $this->addReference('navigation-section-modules-bar', $sectionModuleBar);
        $this->addReference('navigation-global-module-bar', $globalModuleBar);
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