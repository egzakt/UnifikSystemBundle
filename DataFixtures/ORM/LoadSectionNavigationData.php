<?php

namespace Egzakt\Frontend\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Egzakt\SystemBundle\Entity\SectionNavigation;

class LoadSectionNavigationData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $sectionNavigation = new SectionNavigation();
//        $sectionNavigation->setSection();
//        $sectionNavigation->setSection();

        $manager->flush();
    }

    /**
     * Get Order
     *
     * @return int
     */
    public function getOrder()
    {
        return 6;
    }
}
