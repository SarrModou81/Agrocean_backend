<?php

// app/Models/Fournisseur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'contact',
        'telephone',
        'adresse',
        'evaluation',
        'conditions'
    ];

    protected $casts = [
        'evaluation' => 'decimal:2',
    ];

    public function commandeAchats()
    {
        return $this->hasMany(CommandeAchat::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function evaluer()
    {
        $commandes = $this->commandeAchats()->where('statut', 'Reçue')->get();
        $totalCommandes = $commandes->count();

        if ($totalCommandes == 0) return 0;

        $commandesATemps = $commandes->filter(function($cmd) {
            return $cmd->date_livraison_prevue >= $cmd->updated_at;
        })->count();

        return round(($commandesATemps / $totalCommandes) * 5, 2);
    }
}
