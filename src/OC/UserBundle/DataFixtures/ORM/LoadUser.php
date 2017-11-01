<?php

namespace OC\UserBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $em)
    {
        $listNames = ['Loïc', 'Laura', 'Emma'];

        foreach ($listNames as $name) {
            // create an User
            $user = new User();

            // name and password are identicals for now
            $user->setUsername($name);
            $user->setPassword($name);
            // no salt for now
            $user->setSalt('');
            // Set role to user (base role)
            $user->setRoles(['ROLE_USER']);

            $em->persist($user);
        }
        
        $em->flush();
    }
}
