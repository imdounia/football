<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Team;
use App\Entity\Player;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PlayerFixtures extends Fixture
{
    public function getOrder()
    {
        return [
            Team::class,
        ];
    }
    
    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < 9; $i++)
        {
            $entity = new Player();
            $entity
            ->setFirstName("firtname$i")
            ->setLastName("lastname$i")
            ->setNumber(random_int(1,11))
            ->setPortrait("image$i.jpg")
            ->setBirthday(new DateTime('1992-02-05'))
            ;

            $entity->setTeam(
                $this->getReference("refTeam".random_int(0,4))
            );

            $manager->persist($entity);
        }

        $manager->flush();
    }
}
