<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setPassword('$2y$13$KCYHs0w4saOjJZmIXlJuJub8Pf8dYHYYSzbkrLQVQKJjSk8eQaKY2');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPassword('$2y$13$8aprBilGQSeV.yzXRT0Lk.Klj0wN39..iYM1Wv771F4KfZSWZADim');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
