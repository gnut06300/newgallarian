<?php

namespace App\DataFixtures;

use App\Entity\Gallery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class GalleryFixtures extends Fixture implements DependentFixtureInterface
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
            $gallery->setCategory($this->getReference("category-$i"));
    		$manager->persist($gallery);
    	}
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    public function getDependencies(){
        return [
            CategoriesFixture::class
        ];
    }
}
