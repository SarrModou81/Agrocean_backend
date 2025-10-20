<?php

// app/Models/Stock.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id',
        'entrepot_id',
        'quantite',
        'emplacement',
        'date_entree',
        'numero_lot',
        'date_peremption',
        'statut'
    ];

    protected $casts = [
        'date_entree' => 'date',
        'date_peremption' => 'date',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function entrepot()
    {
        return $this->belongsTo(Entrepot::class);
    }

    public function ajusterQuantite($quantite)
    {
        $this->quantite += $quantite;
        $this->save();
    }

    public function verifierPeremption()
    {
        if ($this->date_peremption) {
            $joursRestants = Carbon::now()->diffInDays($this->date_peremption, false);

            if ($joursRestants < 0) {
                $this->statut = 'Périmé';
                $this->save();
                return 'expired';
            } elseif ($joursRestants <= 7) {
                return 'warning';
            }
        }
        return 'ok';
    }

    public function calculerValeur()
    {
        return $this->quantite * $this->produit->prix_achat;
    }
}
