<?php

namespace App\DataFixtures;

use App\Entity\Quartier;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $data =[
            [
                'name' => 'Ngueli Centre de Santé',
                'lat' => '12.065233792427621',
                'lng' =>  '15.06412100616556'
            ],
            [
                'name' => 'Walia Barriel',
                'lat' => '12.080915660292025',
                'lng' =>  '15.102636030720786'
            ],

        ];

        foreach ($data as $value) {
            $quartier = new Quartier();
            $quartier->setNom($value['name']);
            $quartier->setLat($value['lat']);
            $quartier->setLng($value['lng']);
            $manager->persist($quartier);
        }

        $manager->flush();
    }
}
