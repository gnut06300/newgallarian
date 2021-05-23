<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
{ 
	private $encoder;
	public function __construct(UserPasswordEncoderInterface $passwordEncoder){
		$this->encoder = $passwordEncoder;
	}
    public function load(ObjectManager $manager)
    {
        
        for ($i=1; $i <= 10 ; $i++) {
        	$user = new User;
        	$user->setFirstName("Gege_$i");
        	$user->setLastName("Gnut_$i");
        	$user->setEmail("test$i@gnut.eu");
        	$user->setPassword($this->encoder->encodePassword(
                    $user,
                    "password"));
            $user->setIsVerified(true);
        	$manager->persist($user);
        	
        }

        // $product = new Product();
        // $manager->persist($product);
        $manager->flush();
        
    }
}
