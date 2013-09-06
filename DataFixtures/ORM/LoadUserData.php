<?php

namespace Egzakt\Frontend\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Egzakt\SystemBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // This is the default admin user
        // Username: admin
        // Password: defaultadm13
        $defaultAdminUser = new User();
        $defaultAdminUser->setUsername('admin');
        $defaultAdminUser->setFirstname('Default');
        $defaultAdminUser->setLastname('Administrator');
        $defaultAdminUser->setEmail('default_admin@egzakt.com');
        $defaultAdminUser->setPassword('TZSRXoum67+V6PvYhqFNmgx5oHOHKugq9XJCNSGvKZcWstRb5GUNNsZVzFGjiZzZRBIyNBIeoaqVlEOkobH2ig==');
        $defaultAdminUser->setSalt('cbf87f6dffe73a37bf3da648c196c3b5');
        $defaultAdminUser->setActive(true);
        $defaultAdminUser->addRole($manager->merge($this->getReference('role-admin')));

        $manager->persist($defaultAdminUser);
        $manager->flush();
    }

    /**
     * Get Order
     *
     * @return int
     */
    public function getOrder()
    {
        return 7;
    }
}
