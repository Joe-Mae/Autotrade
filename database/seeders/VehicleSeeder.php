<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    private function toCfa(int $euros): int
    {
        return (int) round($euros * 655.957);
    }

    public function run(): void
    {
        $vehicles = [
            [
                'slug' => 'storm-rider-gt',
                'nom' => 'STORM RIDER GT',
                'marque' => 'Auros',
                'categorie' => 'Super Sport',
                'etat' => 'Occasion',
                'annee' => 2024,
                'carburant' => 'V8 Biturbo',
                'boite' => 'Automatique 8 rapports',
                'puissance' => '620 ch',
                'km' => '8 400 km',
                'places' => 4,
                'image' => 'assets/car-storm.jpg',
                'galerie' => ['assets/car-storm.jpg', 'assets/car-vortex.jpg', 'assets/car-heritage.jpg'],
                'prix_vente' => $this->toCfa(128000),
                'disponible' => true,
                'badge' => 'Dispo Immédiate',
                'description' => "Une berline coupé taillée pour l'autoroute comme pour le circuit. Châssis renforcé, échappement sport, intérieur alcantara. Carnet d'entretien complet et garantie 12 mois.",
            ],
            [
                'slug' => 'zenith-pure-x',
                'nom' => 'ZENITH PURE X',
                'marque' => 'Nord',
                'categorie' => 'Luxe & Confort',
                'etat' => 'Neuf',
                'annee' => 2025,
                'carburant' => '100% Électrique',
                'boite' => 'Automatique',
                'puissance' => '480 ch',
                'km' => '2 100 km',
                'places' => 5,
                'image' => 'assets/car-zenith.jpg',
                'galerie' => ['assets/car-zenith.jpg', 'assets/car-electron.jpg', 'assets/car-storm.jpg'],
                'prix_vente' => $this->toCfa(96500),
                'disponible' => true,
                'badge' => 'Édition Limitée',
                'description' => "Le silence absolu au service du confort. 620 km d'autonomie, sièges massants, conduite semi-autonome. Véhicule neuf, garantie constructeur 24 mois.",
            ],
            [
                'slug' => 'apex-proto-1',
                'nom' => 'APEX PROTO-1',
                'marque' => 'Auros',
                'categorie' => 'Super Sport',
                'etat' => 'Occasion',
                'annee' => 2024,
                'carburant' => 'Hybride',
                'boite' => 'Séquentielle 7 rapports',
                'puissance' => '740 ch',
                'km' => '5 900 km',
                'places' => 2,
                'image' => 'assets/car-apex.jpg',
                'galerie' => ['assets/car-apex.jpg', 'assets/car-vortex.jpg', 'assets/car-electron.jpg'],
                'prix_vente' => $this->toCfa(265000),
                'disponible' => true,
                'badge' => 'Best-Seller',
                'description' => 'Notre modèle le plus demandé. 0 à 100 km/h en 2,8 s, aérodynamique active, freins céramique. Révision constructeur à jour, garantie 12 mois incluse.',
            ],
            [
                'slug' => 'vortex-sv',
                'nom' => 'VORTEX SV',
                'marque' => 'Vertex',
                'categorie' => 'Super Sport',
                'etat' => 'Occasion',
                'annee' => 2023,
                'carburant' => 'Essence',
                'boite' => 'Automatique 8 rapports',
                'puissance' => '740 ch',
                'km' => '14 200 km',
                'places' => 2,
                'image' => 'assets/car-vortex.jpg',
                'galerie' => ['assets/car-vortex.jpg', 'assets/car-apex.jpg', 'assets/car-storm.jpg'],
                'prix_vente' => $this->toCfa(210000),
                'disponible' => true,
                'badge' => 'Sur demande',
                'description' => 'Coupé radical en livrée verte signature. Historique complet, entretien tracé en concessions, livraison sur plateau possible.',
            ],
            [
                'slug' => 'electron-z',
                'nom' => 'ELECTRON Z',
                'marque' => 'Nord',
                'categorie' => 'Électrique',
                'etat' => 'Neuf',
                'annee' => 2025,
                'carburant' => '100% Électrique',
                'boite' => 'Automatique',
                'puissance' => '900 ch',
                'km' => '900 km',
                'places' => 2,
                'image' => 'assets/car-electron.jpg',
                'galerie' => ['assets/car-electron.jpg', 'assets/car-zenith.jpg', 'assets/car-apex.jpg'],
                'prix_vente' => $this->toCfa(410000),
                'disponible' => true,
                'badge' => 'Nouveauté',
                'description' => 'Hypercar électrique à quatre moteurs. Une expérience de conduite sans équivalent, sans une goutte de carburant. Livraison et mise en route à domicile.',
            ],
            [
                'slug' => 'heritage-v8',
                'nom' => 'HERITAGE V8',
                'marque' => 'Classic Line',
                'categorie' => 'Luxe & Confort',
                'etat' => 'Occasion',
                'annee' => 1968,
                'carburant' => 'Essence',
                'boite' => 'Manuelle 4 rapports',
                'puissance' => '310 ch',
                'km' => '78 000 km',
                'places' => 4,
                'image' => 'assets/car-heritage.jpg',
                'galerie' => ['assets/car-heritage.jpg', 'assets/car-storm.jpg', 'assets/car-vortex.jpg'],
                'prix_vente' => $this->toCfa(74000),
                'disponible' => true,
                'badge' => 'Collection',
                'description' => "Restauration intégrale atelier, pièces d'origine. Idéale pour collectionneurs, investisseurs et sorties d'exception. Expertise historique fournie.",
            ],
            [
                'slug' => 'grand-nord-suv',
                'nom' => 'GRAND NORD 7',
                'marque' => 'Nord',
                'categorie' => 'SUV',
                'etat' => 'Occasion',
                'annee' => 2024,
                'carburant' => 'Diesel',
                'boite' => 'Automatique 9 rapports',
                'puissance' => '300 ch',
                'km' => '21 500 km',
                'places' => 7,
                'image' => 'assets/car-suv.jpg',
                'galerie' => ['assets/car-suv.jpg', 'assets/car-zenith.jpg', 'assets/car-storm.jpg'],
                'prix_vente' => $this->toCfa(68900),
                'disponible' => true,
                'badge' => 'Familial',
                'description' => 'Sept places, coffre de 780 L et transmission intégrale. Le compagnon des longs trajets, entretien à jour et garantie 12 mois.',
            ],
        ];

        foreach ($vehicles as $v) {
            Vehicle::updateOrCreate(['slug' => $v['slug']], $v);
        }
    }
}
