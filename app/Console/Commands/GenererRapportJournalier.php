<?php
// app/Console/Commands/GenererRapportJournalier.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vente;
use Carbon\Carbon;

class GenererRapportJournalier extends Command
{
    protected $signature = 'rapport:journalier';
    protected $description = 'Génère le rapport journalier des ventes';

    public function handle()
    {
        $aujourdhui = Carbon::today();

        $ventes = Vente::whereDate('date_vente', $aujourdhui)
            ->where('statut', '!=', 'Annulée')
            ->get();

        $this->info("📊 Rapport du " . $aujourdhui->format('d/m/Y'));
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Nombre de ventes: " . $ventes->count());
        $this->info("Chiffre d'affaires: " . number_format($ventes->sum('montant_ttc'), 0, ',', ' ') . " FCFA");
        $this->info("Panier moyen: " . number_format($ventes->avg('montant_ttc'), 0, ',', ' ') . " FCFA");
    }
}
