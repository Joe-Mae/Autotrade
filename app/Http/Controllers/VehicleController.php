<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function catalogue(Request $request)
    {
        $categorie = $request->query('categorie', 'Tous');
        $etat = $request->query('etat', 'Tous');

        $applyFilters = function ($query) use ($categorie, $etat): void {
            if ($categorie !== 'Tous') {
                $query->where('categorie', $categorie);
            }
            if ($etat !== 'Tous') {
                $query->where('etat', $etat);
            }
        };

        $vehicles = Vehicle::aVendre()
            ->disponible()
            ->tap($applyFilters)
            ->orderBy('nom')
            ->get();

        $soldVehicles = Vehicle::aVendre()
            ->where('disponible', false)
            ->tap($applyFilters)
            ->orderBy('nom')
            ->get();

        $categories = ['Tous', 'Super Sport', 'Luxe & Confort', 'Électrique', 'SUV'];
        $etats = ['Tous', 'Neuf', 'Occasion', 'Reconditionné'];

        return view('pages.catalogue', compact('vehicles', 'soldVehicles', 'categories', 'etats', 'categorie', 'etat'));
    }

    public function show(Vehicle $vehicle)
    {
        return view('pages.vehicule', compact('vehicle'));
    }
}
