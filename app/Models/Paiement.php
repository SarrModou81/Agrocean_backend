<?php

// app/Models/Paiement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'facture_id',
        'client_id',
        'fournisseur_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($paiement) {
            if ($paiement->facture) {
                $totalPaiements = $paiement->facture->paiements->sum('montant');

                if ($totalPaiements >= $paiement->facture->montant_ttc) {
                    $paiement->facture->statut = 'Payée';
                } else {
                    $paiement->facture->statut = 'Partiellement Payée';
                }

                $paiement->facture->save();
            }
        });
    }
}
