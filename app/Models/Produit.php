<?php

// app/Models/Produit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'description',
        'categorie_id',
        'prix_achat',
        'prix_vente',
        'seuil_minimum',
        'peremption'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'peremption' => 'boolean',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function detailVentes()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function detailCommandeAchats()
    {
        return $this->hasMany(DetailCommandeAchat::class);
    }

    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }

    public function calculerMarge()
    {
        return $this->prix_vente - $this->prix_achat;
    }

    public function verifierDisponibilite($quantite)
    {
        return $this->stocks()
                ->where('statut', 'Disponible')
                ->sum('quantite') >= $quantite;
    }

    public function stockTotal()
    {
        return $this->stocks()
            ->where('statut', 'Disponible')
            ->sum('quantite');
    }
}
