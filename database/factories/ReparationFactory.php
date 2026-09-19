<?php

namespace Database\Factories;

use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReparationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicule_id' => Vehicule::inRandomOrder()->first()?->id ?? Vehicule::factory(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'duree_main_oeuvre' => $this->faker->randomFloat(1, 0.5, 8),
            'objet_reparation' => $this->faker->randomElement([
                'Vidange', 'Changement plaquettes de frein', 'Réparation moteur',
                'Remplacement pare-brise', 'Diagnostic électronique', 'Changement pneus',
                'Réparation climatisation', 'Contrôle technique', 'Remplacement batterie',
            ]),
        ];
    }
}