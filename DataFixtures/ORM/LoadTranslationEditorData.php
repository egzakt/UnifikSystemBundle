<?php

namespace Flexy\SystemBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Flexy\SystemBundle\Entity\Mapping;

class LoadMappingData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        // Global module bar
        $mapping = new Mapping();
        $mapping->setNavigation($manager->merge($this->getReference('navigation-global-module-bar')));
        $mapping->setApp($manager->merge($this->getReference('app-backend')));
        $mapping->setTarget('FlexySystemBundle:Backend/Translation/Navigation:GlobalModuleBar');
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
        return 8;
    }
}
