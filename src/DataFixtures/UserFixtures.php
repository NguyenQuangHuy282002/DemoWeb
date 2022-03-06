<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User;
        $user->setUsername("admin");
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user,"123456"));
        $manager->persist($user);

        $user = new User;
        $user->setUsername("user");
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user,"123456"));
        $manager->persist($user);


        $manager->flush();
    }
}
