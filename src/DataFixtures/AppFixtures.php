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
                'name' => 'NGOUMNA',
                'lat' => '12.062649606885893,',
                'lng' =>  '15.117999777127254'
            ],
            [
                'name' => 'WALIA',
                'lat' => '12.07615146507117',
                'lng' =>  '15.080582514382355'
            ],
            [
                'name' => 'NGUELI',
                'lat' => '12.065878616147867',
                'lng' =>  '15.065258232534845'
            ],
            [
                'name' => 'TOUKRA',
                'lat' => '12.03530657716086',
                'lng' =>  '15.114005209224786'
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
