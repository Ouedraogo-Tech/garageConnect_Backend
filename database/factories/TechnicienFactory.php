<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TechnicienFactory extends Factory
{
    public function definition(): array
    {
        $noms = ['Ouédraogo', 'Compaoré', 'Kaboré', 'Sawadogo', 'Zongo', 'Traoré', 'Kaboret', 'Bationo'];
        $prenoms = ['Amadou', 'Fatimata', 'Boureima', 'Aïcha', 'Issouf', 'Rasmata', 'Moussa', 'Salimata'];

        return [
            'nom' => $this->faker->randomElement($noms),
            'prenom' => $this->faker->randomElement($prenoms),
            'specialite' => $this->faker->randomElement(['Moteur', 'Carrosserie', 'Électronique', 'Freinage', 'Climatisation']),
        ];
    }
}