<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixture extends Fixture
{
    private $sluger;

    public function __construct(SluggerInterface $sluggerInterface)
    {
        $this->sluger = $sluggerInterface;
    }

    public function load(ObjectManager $manager)
    {
        for ($i=1; $i <= 10; $i++) { 
            $categories = new Category;
            $categories->setName("Brouette $i");
            $categories->setContent("ma brouette $i");
            $categories->setSlug($this->sluger->slug($categories->getName())->lower());
            $manager->persist($categories);
        }
        $manager->flush();
    }
}