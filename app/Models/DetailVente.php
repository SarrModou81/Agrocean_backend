<?php

// app/Models/DetailVente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailVente extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'sous_total'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'sous_total' => 'decimal:2',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detailVente) {
            $detailVente->sous_total = $detailVente->quantite * $detailVente->prix_unitaire;
        });

        static::updating(function ($detailVente) {
            $detailVente->sous_total = $detailVente->quantite * $detailVente->prix_unitaire;
        });
    }
}
