<?php

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TeamFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 4; $i++) { 
            $entity = new Team();
            $entity
            ->setName("team$i")
            ->setFlag("image$i.jpg")
            ;

            $this->addReference("refTeam$i", $entity);

            $manager->persist($entity);
        }

        $manager->flush();
    }
}
