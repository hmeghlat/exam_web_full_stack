<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Hamster;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {   $user = $this->createUser('Admin@example.com', 'password',['ROLE_ADMIN']);
        $hamster = $this->createHamster($user);
        $manager->persist($user);
        $manager->persist($hamster);
        $manager->flush();
    }
    public function createUser(string $email, string $password, array $roles): User
    {
        $user = new User();
        $user->setEmail($email);
        // password is hashed using security.yaml encoders
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles($roles);
        return $user;
    }
    public function createHamster(User $owner): Hamster
    {   $faker = \Faker\Factory::create('fr_FR');
        $hamster = new Hamster();
        $hamster->setName($faker->name);
        $hamster->setAge($faker->numberBetween(1, 500));
        $hamster->setHunger($faker->numberBetween(1, 100));
        $hamster->setGenre($faker->randomElement(['M', 'F']));
        $hamster->setOwner($owner);
        $hamster->setActive(true);
        return $hamster;
    }

}
