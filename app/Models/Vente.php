<?php

// app/Models/Vente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'client_id',
        'user_id',
        'date_vente',
        'montant_ht',
        'montant_ttc',
        'remise',
        'statut'
    ];

    protected $casts = [
        'date_vente' => 'date',
        'montant_ht' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'remise' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailVentes()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function facture()
    {
        return $this->hasOne(Facture::class);
    }

    public function livraison()
    {
        return $this->hasOne(Livraison::class);
    }

    public function calculerTotal()
    {
        $sousTotal = $this->detailVentes->sum('sous_total');
        $this->montant_ht = $sousTotal - $this->remise;
        $this->montant_ttc = $this->montant_ht * 1.18; // TVA 18%
        $this->save();
    }

    public function genererFacture()
    {
        return Facture::create([
            'numero' => 'F' . date('Y') . str_pad($this->id, 6, '0', STR_PAD_LEFT),
            'vente_id' => $this->id,
            'date_emission' => now(),
            'date_echeance' => now()->addDays(30),
            'montant_ttc' => $this->montant_ttc,
            'statut' => 'Impayée'
        ]);
    }

    public function annuler()
    {
        if ($this->statut != 'Livrée') {
            foreach ($this->detailVentes as $detail) {
                $stock = Stock::where('produit_id', $detail->produit_id)
                    ->where('statut', 'Disponible')
                    ->first();

                if ($stock) {
                    $stock->ajusterQuantite($detail->quantite);
                }
            }

            $this->statut = 'Annulée';
            $this->save();
        }
    }
}
