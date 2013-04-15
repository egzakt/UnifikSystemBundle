<?php

namespace Egzakt\System\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

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
        $localeFr = new Locale();
        $localeFr->setName('Français');
        $localeFr->setSwitchName('Aller en français');
        $localeFr->setCode('fr');
        $localeFr->setActive(true);

        $manager->persist($localeFr);

        $localeEn = new Locale();
        $localeEn->setName('English');
        $localeEn->setSwitchName('Switch to English');
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