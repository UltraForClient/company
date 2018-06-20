<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setStreet('admin');
        $user->setPlace('admin');
        $user->setPostCode('admin');
        $user->setHouseNumber('admin');
        $user->setMode('admin');
        $user->setPhone('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setName('admin');
        $user->setSurname('admin');

        $user->setEmail('admin@zws.com.pl');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));

        $manager->persist($user);
        $manager->flush();
    }
}
