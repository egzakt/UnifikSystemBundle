<?php

namespace Egzakt\System\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;

use Egzakt\SystemBundle\Entity\Locale;

/**
 * Load BlogCategory Data
 */
class LoadLocaleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetaData('Egzakt\\SystemBundle\\Entity\\Locale');
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $localeFr = new Locale();
        $localeFr->setId(1);
        $localeFr->setName('Français');
        $localeFr->setCode('fr');
        $localeFr->setActive(true);

        $manager->persist($localeFr);

        $localeEn = new Locale();
        $localeEn->setId(2);
        $localeEn->setName('English');
        $localeEn->setCode('en');
        $localeEn->setActive(true);

        $manager->persist($localeEn);

        $manager->flush();

        $this->addReference('locale-fr', $localeFr);
        $this->addReference('locale-en', $localeEn);
    }

    /**
     * Get Order
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}