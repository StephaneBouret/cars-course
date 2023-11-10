<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User;

        $hash = $this->passwordHasher->hashPassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
            ->setFirstname("Vincent")
            ->setLastname("Parot")
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $hash = $this->passwordHasher->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setPassword($hash);

            $manager->persist($user);
        }

        for ($c=0; $c < 4; $c++) { 
            $comment = new Comment;
            $comment->setFullname($faker->name())
                    ->setContent($faker->paragraph())
                    ->setRating(mt_rand(1,5))
                    ->setIsValid(false);

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
