<?php

namespace App\View\Components;

use App\Models\Vehicule;
use Illuminate\View\Component;

class VehiculeCard extends Component
{
    public Vehicule $vehicule;

    public function __construct(Vehicule $vehicule)
    {
        $this->vehicule = $vehicule;
    }

    public function render()
    {
        return view('components.vehicule-card');
    }
}