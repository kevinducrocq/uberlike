<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 16; $i++) {

            $user = new User();
            $factory = Factory::create('fr_FR');
            $user->setUsername($factory->username);
            $user->setEmail($factory->safeEmail);
            $pwd = $this->encoder->hashPassword($user, '123456');
            $user->setPassword($pwd);
            $user->setAddress($factory->streetAddress);
            $user->setZip($factory->postCode);
            $user->setCity($factory->city);
            $user->setLatitude($factory->latitude(2, 3));
            $user->setLongitude($factory->longitude(47, 48));
            $user->setAvatar('64_' . $i . '.png');
            $manager->persist($user);
        }
        $manager->flush();
    }
}
