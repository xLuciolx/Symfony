<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
    // $manager is the EntityManager
    public function load(ObjectManager $manager)
    {
        // Category list
        $names = array(
            'Développement web',
            'Développement mobile',
            'Graphisme',
            'Intégration',
            'Réseau'
        );

        foreach ($names as $name) {
            // create the category
            $category = new Category();
            $category->setName($name);

            //Persist
            $manager->persist($category);
        }

        //Save
        $manager->flush();
    }
}
