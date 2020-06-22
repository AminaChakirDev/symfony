<?php


namespace App\DataFixtures;


use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{

    const ACTORS = [
        'Bryan Cranston',
        'Kim Dickens',
        'Henry Thomas',
        'Andrew Lincoln',
        'Scott Whyte',
        'Josh Hartnett',
    ];

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        foreach (self::ACTORS as $key => $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);

            $manager->persist($actor);
            $actor->addProgram($this->getReference('program_' . $key));
            $this->addReference('actor_' . $key, $actor);
        }

        $faker = Faker\Factory::create('en_US');

        for ($i = 0; $i < 50; $i++) {
            $actorFaker = new Actor();
            $actorFaker->setName($faker->name);

            $manager->persist($actorFaker);
            $actorFaker->addProgram($this->getReference('program_' . rand(0,5)));
            $this->setReference('actor_' . $i, $actorFaker);

        }

        $manager->flush();
    }
}