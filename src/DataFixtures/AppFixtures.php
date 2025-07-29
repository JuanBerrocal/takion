<?php

namespace App\DataFixtures;

use App\Entity\TKuser;
use App\Factory\TKUserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        TKUserFactory::createOne(['email' => 'juan.brudner@gmail.com', 'firstName' => 'Juan', 'secondName' => 'Berrocal']);
        TKUserFactory::createMany(25);

        $manager->flush();
    }
}
