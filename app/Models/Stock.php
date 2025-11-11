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

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    /**
     * Ajuster la quantité en stock avec validation et traçabilité
     */
    public function ajusterQuantite($quantite, $motif = null, $referenceType = null, $referenceId = null)
    {
        $nouvelleQuantite = $this->quantite + $quantite;

        if ($nouvelleQuantite < 0) {
            throw new \Exception("La quantité ne peut pas être négative pour le stock ID: {$this->id}. Quantité actuelle: {$this->quantite}, ajustement demandé: {$quantite}");
        }

        // Déterminer le type de mouvement
        $type = 'Ajustement';
        if ($referenceType === 'Vente') {
            $type = 'Sortie';
        } elseif ($referenceType === 'CommandeAchat') {
            $type = 'Entrée';
        } elseif ($quantite > 0) {
            $type = 'Entrée';
        } elseif ($quantite < 0) {
            $type = 'Sortie';
        }

        // Créer le mouvement de stock pour traçabilité
        MouvementStock::create([
            'type' => $type,
            'stock_id' => $this->id,
            'produit_id' => $this->produit_id,
            'entrepot_id' => $this->entrepot_id,
            'quantite' => $quantite,
            'numero_lot' => $this->numero_lot,
            'motif' => $motif ?? "Ajustement de stock",
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'user_id' => auth()->id(),
            'date' => now()
        ]);

        // Mettre à jour la quantité
        $this->quantite = $nouvelleQuantite;

        // Vérifier la péremption
        if ($this->date_peremption && Carbon::now()->greaterThan($this->date_peremption)) {
            $this->statut = 'Périmé';
        }

        $this->save();

        return $this;
    }

    /**
     * Vérifier l'état de péremption du produit
     */
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

    /**
     * Calculer la valeur totale du stock
     */
    public function calculerValeur()
    {
        if (!$this->produit) {
            return 0;
        }
        return $this->quantite * $this->produit->prix_achat;
    }

    /**
     * Scope pour stocks disponibles
     */
    public function scopeDisponible($query)
    {
        return $query->where('statut', 'Disponible')
            ->where('quantite', '>', 0);
    }

    /**
     * Scope pour stocks expirés ou proche expiration
     */
    public function scopeExpirationProche($query, $jours = 7)
    {
        return $query->whereNotNull('date_peremption')
            ->whereDate('date_peremption', '<=', Carbon::now()->addDays($jours))
            ->whereDate('date_peremption', '>=', Carbon::now());
    }
}
