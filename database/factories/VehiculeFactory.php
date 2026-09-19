<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculeFactory extends Factory
{
    public function definition(): array
    {
        $modeles = [
            'Toyota' => ['Corolla', 'RAV4', 'Yaris'],
            'Peugeot' => ['208', '3008', '308'],
            'Renault' => ['Clio', 'Duster', 'Megane'],
            'Hyundai' => ['Tucson', 'i10', 'Accent'],
            'Ford' => ['Ranger', 'Focus', 'Fiesta'],
            'Kia' => ['Sportage', 'Picanto', 'Rio'],
            'Suzuki' => ['Vitara', 'Swift', 'Jimny'],
            'Nissan' => ['Qashqai', 'Micra', 'Navara'],
            'Volkswagen' => ['Golf', 'Polo', 'Tiguan'],
            'Mitsubishi' => ['Pajero', 'L200', 'ASX'],
        ];

        $marque = $this->faker->randomElement(array_keys($modeles));
        $modele = $this->faker->randomElement($modeles[$marque]);

        return [
            'immatriculation' => '11-BF-' . $this->faker->unique()->numberBetween(1000, 9999),
            'marque' => $marque,
            'modele' => $modele,
            'couleur' => $this->faker->randomElement(['Blanc', 'Noir', 'Gris', 'Rouge', 'Bleu', 'Argent', 'Marron']),
            'annee' => $this->faker->numberBetween(2010, 2026),
            'kilometrage' => $this->faker->numberBetween(5000, 220000),
            'carrosserie' => $this->faker->randomElement(['Berline', 'SUV', 'Break', 'Citadine', 'Pick-up']),
            'energie' => $this->faker->randomElement(['Essence', 'Diesel', 'Électrique', 'Hybride']),
            'boite' => $this->faker->randomElement(['Manuelle', 'Automatique']),
        ];
    }
}