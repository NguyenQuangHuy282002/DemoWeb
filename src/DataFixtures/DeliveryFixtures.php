<?php

namespace App\DataFixtures;

use App\Entity\Delivery;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class DeliveryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $delivery = new Delivery;
            $delivery->setName("Delivery $i");
            $delivery->setDescription("Delivery $i");
            $delivery->setImage("https://image.shutterstock.com/image-vector/delivery-courier-carrying-packages-truck-260nw-1897067275.jpg");
            $manager->persist($delivery);
        }
        $manager->flush();
    }
}
