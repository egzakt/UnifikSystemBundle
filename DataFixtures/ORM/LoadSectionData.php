<?php

namespace Egzakt\Frontend\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Entity\SectionTranslation;

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
        $metadata = $manager->getClassMetaData('Egzakt\\SystemBundle\\Entity\\Section');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $sectionHome = new Section();
        $sectionHome->setId(1);
        $sectionHome->setContainer($this->container);
        $sectionHome->setApp($manager->merge($this->getReference('app-frontend')));

        $sectionHomeFr = new SectionTranslation();
        $sectionHomeFr->setLocale($manager->merge($this->getReference('locale-fr'))->getCode());
        $sectionHomeFr->setName('Accueil');
        $sectionHomeFr->setActive(true);
        $sectionHomeFr->setTranslatable($sectionHome);

        $sectionHomeEn = new SectionTranslation();
        $sectionHomeEn->setLocale($manager->merge($this->getReference('locale-en'))->getCode());
        $sectionHomeEn->setName('Home');
        $sectionHomeEn->setActive(true);
        $sectionHomeEn->setTranslatable($sectionHome);

        $manager->persist($sectionHome);
        $manager->persist($sectionHomeFr);
        $manager->persist($sectionHomeEn);
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
