<?php

namespace App\DataFixtures;

use App\Entity\Supplier;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class SupplierFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $supplier = new Supplier;
            $supplier->setName("Supplier $i");
            $supplier->setDescription("Local Supplier $i");
            $supplier->setImage("package.jpeg");
            $manager->persist($supplier);
        }
        $manager->flush();
    }
}
