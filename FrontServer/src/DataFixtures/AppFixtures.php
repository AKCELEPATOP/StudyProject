<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('aleksandr.zemlyanskiy@simbirsoft.com');

        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setSuperAdmin(true);
        $user->setEnabled(true);
        $manager->persist($user);

        $manager->flush();
    }
}
