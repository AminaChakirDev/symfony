<?php


namespace App\DataFixtures;


use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class ActorFixtures extends Fixture
{

    const ACTORS = [
        'Bryan Cranston',
        'Kim Dickens',
        'Henry Thomas',
        'Andrew Lincoln',
        'Scott Whyte',
        'Josh Hartnett',
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::ACTORS as $key => $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);

            $manager->persist($actor);

            $this->addReference('actor_' . $key, $actor);
        }

        $faker = Faker\Factory::create('en_US');

        for ($i = 6; $i < 56; $i++) {
            $actorFaker = new Actor();
            $actorFaker->setName($faker->name);

            $manager->persist($actorFaker);

            $this->addReference('actor_' . $i, $actorFaker);

        }

        $manager->flush();
    }
}