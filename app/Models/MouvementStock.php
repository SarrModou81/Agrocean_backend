<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    protected $table = 'mouvements_stock';

    protected $fillable = [
        'type', // Entrée, Sortie, Ajustement
        'stock_id',
        'produit_id',
        'entrepot_id',
        'quantite', // Positif pour entrée, négatif pour sortie
        'numero_lot',
        'motif',
        'reference_type', // Vente, CommandeAchat, Ajustement
        'reference_id', // ID de la vente, commande, etc.
        'user_id',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
        'quantite' => 'integer'
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function entrepot()
    {
        return $this->belongsTo(Entrepot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
