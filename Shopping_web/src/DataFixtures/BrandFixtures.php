<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $brand = new Brand;
            $brand->setName("Brand $i");
            $brand->setDescription("Local Brand $i");
            $brand->setImage("https://cf.shopee.vn/file/81b8325d9cd6c1a058c3b58fc65fdf02");
            $manager->persist($brand);
        }
        $manager->flush();
    }
}
