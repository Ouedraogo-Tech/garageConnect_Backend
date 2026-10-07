<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Liste de tous les clients (pour les listes déroulantes admin)
    public function index()
    {
        return Client::orderBy('nom')->get();
    }
}
