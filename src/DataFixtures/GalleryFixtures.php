<?php

namespace App\DataFixtures;

use App\Entity\Gallery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class GalleryFixtures extends Fixture
{
	private $sluger;

	public function __construct(SluggerInterface $sluggerInterface)
    {
        $this->sluger = $sluggerInterface;
    }
    
    public function load(ObjectManager $manager)
    {
    	for ($i=1; $i <= 10; $i++) {
    		$gallery = new Gallery(); 
    		$gallery->setTitle("Titre_$i");
    		$gallery->setDescription("Description_$i");
    		$gallery->setSlug($this->sluger->slug($gallery->getTitle())->lower());
    		$manager->persist($gallery);
    	}
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
