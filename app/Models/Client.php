<?php

// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'type',
        'credit_max',
        'solde'
    ];

    protected $casts = [
        'credit_max' => 'decimal:2',
        'solde' => 'decimal:2',
    ];

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function calculerSolde()
    {
        $totalVentes = $this->ventes()->where('statut', '!=', 'Annulée')->sum('montant_ttc');
        $totalPaiements = $this->paiements()->sum('montant');
        return $totalVentes - $totalPaiements;
    }
}

