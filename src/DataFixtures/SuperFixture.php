<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class SuperFixture extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {

        $user = new User();

        $user->setPassword($this->passwordEncoder->encodePassword($user, 'passer'));
        $user->setUsername("toufa");
        $user->setRoles(["ROLE_SUPERADMIN"]);
        $user->setNom("Fatou Diouf");
        $user->setEmail("toufa@toufa.com");
        $user->setAdresse("Corniche");
        $user->setTelephone("778418109");
        $user->setStatut("bloqué");
        $user->setPartenaire(NULL);
        $user->setImageName("admin.jpg");
        
        
        $manager->persist($user);
        $manager->flush();   
    }
}
