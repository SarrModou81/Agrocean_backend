<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use App\Models\Produit;
use App\Models\Alerte;

class VerifierStocks extends Command
{
    protected $signature = 'stock:verifier';
    protected $description = 'Vérifie les stocks et génère des alertes';

    public function handle()
    {
        $this->info('Vérification des stocks en cours...');

        // Vérifier les péremptions
        $stocks = Stock::where('statut', 'Disponible')
            ->whereNotNull('date_peremption')
            ->get();

        foreach ($stocks as $stock) {
            $etat = $stock->verifierPeremption();

            if ($etat == 'warning' || $etat == 'expired') {
                $this->warn("⚠️ Alerte péremption: {$stock->produit->nom}");
            }
        }

        // Vérifier les stocks faibles
        $produits = Produit::all();

        foreach ($produits as $produit) {
            $stockTotal = $produit->stockTotal();

            if ($stockTotal == 0) {
                $this->error("❌ Rupture: {$produit->nom}");
            } elseif ($stockTotal < $produit->seuil_minimum) {
                $this->warn("⚠️ Stock faible: {$produit->nom} ({$stockTotal} unités)");
            }
        }

        $this->info('✅ Vérification terminée');
    }
}
