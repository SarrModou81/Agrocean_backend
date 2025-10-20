<?php

// app/Models/Livraison.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'date_prevue',
        'date_effective',
        'adresse',
        'statut',
        'livreur'
    ];

    protected $casts = [
        'date_prevue' => 'date',
        'date_effective' => 'date',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function planifier()
    {
        $this->statut = 'Planifiée';
        $this->save();
    }

    public function confirmer()
    {
        $this->date_effective = now();
        $this->statut = 'Livrée';
        $this->save();

        $this->vente->statut = 'Livrée';
        $this->vente->save();
    }
}
