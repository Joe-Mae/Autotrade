<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'slug', 'nom', 'marque', 'categorie', 'etat', 'annee', 'carburant', 'boite',
        'puissance', 'km', 'places', 'image', 'galerie', 'prix_vente',
        'disponible', 'badge', 'description',
    ];

    protected $casts = [
        'galerie' => 'array',
        'disponible' => 'boolean',
        'prix_vente' => 'integer',
    ];

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function scopeAVendre($query)
    {
        return $query->whereNotNull('prix_vente');
    }

    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    /**
     * Libellé affiché dans la « case » de statut sur les vignettes.
     * Un véhicule en vente sans badge personnalisé affiche « En vente ».
     */
    public function getBadgeLabelAttribute(): string
    {
        if (! $this->disponible) {
            return 'Vendu';
        }

        return $this->badge ?: 'En vente';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function cfa(?int $n): string
    {
        if ($n === null) {
            return 'Sur devis';
        }

        return number_format($n, 0, ',', ' ').' FCFA';
    }
}
