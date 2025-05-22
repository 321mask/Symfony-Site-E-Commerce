<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Faker\Factory;
use Mmo\Faker\PicsumProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->addProvider(new PicsumProvider($faker));

        for ($i = 1; $i <= 20; $i++) {
            $categorie = $this->getReference('categorie_'.$faker->numberBetween(1, 10), Category::class);
    
            $path = $faker->picsum(
                $dir = "C:\laragon\www\myboutique\public\uploads",
                $width = 640,
                $height = 480,
                $fullPath = true,
                $id = null,
                $randomize = true,
                $gray = false,
                $blur = null,
                $imageExtension = null
            );
    
            $image= str_replace('C:\laragon\www\myboutique\public\uploads\\', '', $path);
    
            $product = new Product();
            $product->setCategory($categorie)
                ->setName($faker->words(3, true))
                ->setDescription($faker->paragraph(2))
                ->setPrice($faker->numberBetween(100, 100000))
                ->setImage($image)
                ->setSubtitle($faker->sentence());
                // ->setSlug($faker->word());
            $manager->persist($product);
    
            $manager->flush();
        }
    }
}
