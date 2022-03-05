<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i<=100; $i++) {
            $product = new Product;
            $product->setName("product $i");
            $product->setImage("https://toanmobile.vn/userdata/8264/wp-content/uploads/2021/10/iphone-13-mini-select-2021-jpeg.jpeg");
            $product->setDescription("adjkfhjkfsd");
            $product->setPrice((float)rand(120,500));
            $product->setDate(\DateTime::createFromFormat('Y/m/d','2022/5/16'));
            $product->setQuantity(rand(12,100));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
