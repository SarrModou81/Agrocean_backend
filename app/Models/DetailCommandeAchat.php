<?php

// app/Models/DetailCommandeAchat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailCommandeAchat extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_achat_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'sous_total'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'sous_total' => 'decimal:2',
    ];

    public function commandeAchat()
    {
        return $this->belongsTo(CommandeAchat::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail) {
            $detail->sous_total = $detail->quantite * $detail->prix_unitaire;
        });
    }
}
