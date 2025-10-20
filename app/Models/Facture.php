<?php

// app/Models/Facture.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'vente_id',
        'date_emission',
        'date_echeance',
        'montant_ttc',
        'statut'
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_echeance' => 'date',
        'montant_ttc' => 'decimal:2',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function genererPDF()
    {
        // Logique de génération PDF
    }

    public function envoyer()
    {
        // Logique d'envoi par email
    }
}
