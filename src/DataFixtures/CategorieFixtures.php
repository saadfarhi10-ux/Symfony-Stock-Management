<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['nom' => 'Fruits et légumes', 'description' => 'Produits frais : fruits et légumes'],
            ['nom' => 'Produits laitiers', 'description' => 'Lait, fromage, yaourts'],
            ['nom' => 'Boucherie', 'description' => 'Viandes et volailles'],
            ['nom' => 'Épicerie', 'description' => 'Pâtes, riz, conserves'],
            ['nom' => 'Boissons', 'description' => 'Eaux, sodas, jus'],
            ['nom' => 'Surgelés', 'description' => 'Produits congelés'],
            ['nom' => 'Hygiène', 'description' => 'Produits d’hygiène et entretien'],
        ];

        foreach ($categories as $data) {
            $categorie = new Categorie();
            $categorie->setNom($data['nom']);
            $categorie->setDescription($data['description']);

            $manager->persist($categorie);
        }

        $manager->flush();
    }
}
