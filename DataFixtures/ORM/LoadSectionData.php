<?php

namespace Unifik\Frontend\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Unifik\SystemBundle\Entity\Section;
use Unifik\SystemBundle\Entity\SectionTranslation;

class LoadSectionData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $metadata = $manager->getClassMetaData('Unifik\\SystemBundle\\Entity\\Section');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $sectionHome = new Section();
        $sectionHome->setId(1);
        $sectionHome->setContainer($this->container);
        $sectionHome->setApp($manager->merge($this->getReference('app-frontend')));

        $sectionHome->setCurrentLocale($manager->merge($this->getReference('locale-fr'))->getCode());
        $sectionHome->setName('Accueil');
        $sectionHome->setActive(true);

        $sectionHome->setCurrentLocale($manager->merge($this->getReference('locale-en'))->getCode());
        $sectionHome->setName('Home');
        $sectionHome->setActive(true);

        $manager->persist($sectionHome);
        $manager->flush();

        $this->addReference('section-home', $sectionHome);
    }

    /**
     * Get Order
     *
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
