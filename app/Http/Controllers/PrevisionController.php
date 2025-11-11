<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Stock;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrevisionController extends Controller
{
    /**
     * Obtenir les prévisions de réapprovisionnement pour tous les produits
     */
    public function previsions(Request $request)
    {
        $periode = $request->input('periode', 30); // Période en jours (par défaut 30)
        $seuil_alerte = $request->input('seuil_alerte', 0.8); // 80% du stock minimum

        $produits = Produit::with(['stocks', 'categorie'])->get();
        $previsions = [];

        foreach ($produits as $produit) {
            $prevision = $this->calculerPrevisionProduit($produit, $periode, $seuil_alerte);
            if ($prevision['necessite_reapprovisionnement']) {
                $previsions[] = $prevision;
            }
        }

        // Trier par priorité (score de risque)
        usort($previsions, function($a, $b) {
            return $b['score_risque'] <=> $a['score_risque'];
        });

        return response()->json([
            'success' => true,
            'periode_analyse' => $periode,
            'total_produits_analyses' => count($produits),
            'produits_necessite_reapprovisionnement' => count($previsions),
            'previsions' => $previsions
        ]);
    }

    /**
     * Obtenir la prévision pour un produit spécifique
     */
    public function previsionProduit($produitId, Request $request)
    {
        $periode = $request->input('periode', 30);
        $produit = Produit::with(['stocks', 'categorie'])->findOrFail($produitId);

        $prevision = $this->calculerPrevisionProduit($produit, $periode, 0.8);

        return response()->json([
            'success' => true,
            'prevision' => $prevision
        ]);
    }

    /**
     * Calculer la prévision de réapprovisionnement pour un produit
     */
    private function calculerPrevisionProduit($produit, $periode, $seuil_alerte)
    {
        $dateDebut = Carbon::now()->subDays($periode);

        // 1. Calculer le stock actuel total
        $stockActuel = $produit->stocks()->sum('quantite');

        // 2. Calculer la consommation moyenne quotidienne
        $ventesHistoriques = DetailVente::where('produit_id', $produit->id)
            ->whereHas('vente', function($query) use ($dateDebut) {
                $query->where('date_vente', '>=', $dateDebut)
                      ->whereIn('statut', ['Validée', 'Livrée']);
            })
            ->sum('quantite');

        $consommationMoyenneJour = $periode > 0 ? $ventesHistoriques / $periode : 0;

        // 3. Calculer les jours de stock restants
        $joursStockRestant = $consommationMoyenneJour > 0
            ? $stockActuel / $consommationMoyenneJour
            : 999;

        // 4. Analyser la tendance (croissance ou décroissance des ventes)
        $tendance = $this->analyserTendance($produit->id, $periode);

        // 5. Calculer la quantité recommandée
        $quantiteRecommandee = $this->calculerQuantiteRecommandee(
            $produit,
            $consommationMoyenneJour,
            $tendance
        );

        // 6. Date estimée de rupture de stock
        $dateRuptureEstimee = $consommationMoyenneJour > 0
            ? Carbon::now()->addDays(floor($joursStockRestant))
            : null;

        // 7. Score de risque (0-100)
        $scoreRisque = $this->calculerScoreRisque(
            $stockActuel,
            $produit->seuil_minimum,
            $joursStockRestant,
            $consommationMoyenneJour
        );

        // 8. Nécessite réapprovisionnement ?
        $necessiteReapprovisionnement =
            $stockActuel <= ($produit->seuil_minimum * $seuil_alerte) ||
            $joursStockRestant <= 7 ||
            $scoreRisque >= 70;

        return [
            'produit_id' => $produit->id,
            'produit_code' => $produit->code,
            'produit_nom' => $produit->nom,
            'categorie' => $produit->categorie->nom ?? 'N/A',
            'stock_actuel' => $stockActuel,
            'seuil_minimum' => $produit->seuil_minimum,
            'consommation_moyenne_jour' => round($consommationMoyenneJour, 2),
            'jours_stock_restant' => round($joursStockRestant, 1),
            'date_rupture_estimee' => $dateRuptureEstimee?->format('Y-m-d'),
            'quantite_recommandee' => $quantiteRecommandee,
            'tendance' => $tendance,
            'score_risque' => $scoreRisque,
            'necessite_reapprovisionnement' => $necessiteReapprovisionnement,
            'priorite' => $this->determinerPriorite($scoreRisque),
            'ventes_periode' => $ventesHistoriques,
            'periode_jours' => $periode
        ];
    }

    /**
     * Analyser la tendance des ventes
     */
    private function analyserTendance($produitId, $periode)
    {
        $milieu = $periode / 2;
        $dateDebut = Carbon::now()->subDays($periode);
        $dateMilieu = Carbon::now()->subDays($milieu);

        // Ventes première moitié
        $ventesPremiereMoitie = DetailVente::where('produit_id', $produitId)
            ->whereHas('vente', function($query) use ($dateDebut, $dateMilieu) {
                $query->whereBetween('date_vente', [$dateDebut, $dateMilieu])
                      ->whereIn('statut', ['Validée', 'Livrée']);
            })
            ->sum('quantite');

        // Ventes deuxième moitié
        $ventesDeuxiemeMoitie = DetailVente::where('produit_id', $produitId)
            ->whereHas('vente', function($query) use ($dateMilieu) {
                $query->where('date_vente', '>=', $dateMilieu)
                      ->whereIn('statut', ['Validée', 'Livrée']);
            })
            ->sum('quantite');

        if ($ventesPremiereMoitie == 0 && $ventesDeuxiemeMoitie == 0) {
            return ['type' => 'stable', 'pourcentage' => 0];
        }

        if ($ventesPremiereMoitie == 0) {
            return ['type' => 'croissante', 'pourcentage' => 100];
        }

        $evolution = (($ventesDeuxiemeMoitie - $ventesPremiereMoitie) / $ventesPremiereMoitie) * 100;

        if ($evolution > 10) {
            return ['type' => 'croissante', 'pourcentage' => round($evolution, 2)];
        } elseif ($evolution < -10) {
            return ['type' => 'decroissante', 'pourcentage' => round($evolution, 2)];
        } else {
            return ['type' => 'stable', 'pourcentage' => round($evolution, 2)];
        }
    }

    /**
     * Calculer la quantité recommandée pour la commande
     */
    private function calculerQuantiteRecommandee($produit, $consommationMoyenneJour, $tendance)
    {
        // Stock de sécurité (15 jours de stock)
        $joursStockSecurite = 15;
        $stockSecurite = $consommationMoyenneJour * $joursStockSecurite;

        // Délai de livraison estimé (7 jours par défaut)
        $delaiLivraison = 7;
        $consommationPendantDelai = $consommationMoyenneJour * $delaiLivraison;

        // Stock optimal (30 jours de ventes)
        $stockOptimal = $consommationMoyenneJour * 30;

        // Ajustement selon la tendance
        $facteurTendance = 1.0;
        if ($tendance['type'] === 'croissante') {
            $facteurTendance = 1 + (abs($tendance['pourcentage']) / 100);
        } elseif ($tendance['type'] === 'decroissante') {
            $facteurTendance = 1 - (abs($tendance['pourcentage']) / 200); // Moins agressif
        }

        $stockActuel = $produit->stocks()->sum('quantite');
        $quantiteRecommandee = ($stockOptimal + $stockSecurite + $consommationPendantDelai - $stockActuel) * $facteurTendance;

        return max(0, ceil($quantiteRecommandee));
    }

    /**
     * Calculer le score de risque de rupture (0-100)
     */
    private function calculerScoreRisque($stockActuel, $seuilMinimum, $joursStockRestant, $consommationMoyenne)
    {
        $score = 0;

        // Critère 1: Niveau de stock par rapport au seuil (40 points max)
        if ($seuilMinimum > 0) {
            $ratioStock = $stockActuel / $seuilMinimum;
            if ($ratioStock <= 0.5) {
                $score += 40;
            } elseif ($ratioStock <= 1) {
                $score += 30;
            } elseif ($ratioStock <= 1.5) {
                $score += 15;
            }
        }

        // Critère 2: Jours de stock restant (40 points max)
        if ($joursStockRestant <= 3) {
            $score += 40;
        } elseif ($joursStockRestant <= 7) {
            $score += 30;
        } elseif ($joursStockRestant <= 14) {
            $score += 20;
        } elseif ($joursStockRestant <= 21) {
            $score += 10;
        }

        // Critère 3: Vélocité des ventes (20 points max)
        if ($consommationMoyenne > 10) {
            $score += 20;
        } elseif ($consommationMoyenne > 5) {
            $score += 10;
        } elseif ($consommationMoyenne > 2) {
            $score += 5;
        }

        return min(100, $score);
    }

    /**
     * Déterminer la priorité selon le score de risque
     */
    private function determinerPriorite($scoreRisque)
    {
        if ($scoreRisque >= 80) {
            return 'CRITIQUE';
        } elseif ($scoreRisque >= 60) {
            return 'HAUTE';
        } elseif ($scoreRisque >= 40) {
            return 'MOYENNE';
        } else {
            return 'BASSE';
        }
    }

    /**
     * Générer automatiquement des commandes d'achat recommandées
     */
    public function genererCommandesRecommandees(Request $request)
    {
        $periode = $request->input('periode', 30);
        $scoreMinimum = $request->input('score_minimum', 60); // Seuil de risque minimum

        $produits = Produit::with(['stocks', 'categorie'])->get();
        $commandesRecommandees = [];

        foreach ($produits as $produit) {
            $prevision = $this->calculerPrevisionProduit($produit, $periode, 0.8);

            if ($prevision['score_risque'] >= $scoreMinimum) {
                // Trouver le meilleur fournisseur pour ce produit
                $fournisseur = $this->trouverMeilleurFournisseur($produit->id);

                $commandesRecommandees[] = [
                    'produit' => [
                        'id' => $produit->id,
                        'code' => $produit->code,
                        'nom' => $produit->nom
                    ],
                    'quantite_recommandee' => $prevision['quantite_recommandee'],
                    'fournisseur' => $fournisseur,
                    'priorite' => $prevision['priorite'],
                    'score_risque' => $prevision['score_risque'],
                    'date_rupture_estimee' => $prevision['date_rupture_estimee'],
                    'prix_estime' => $produit->prix_achat * $prevision['quantite_recommandee']
                ];
            }
        }

        // Trier par priorité
        usort($commandesRecommandees, function($a, $b) {
            return $b['score_risque'] <=> $a['score_risque'];
        });

        $coutTotal = array_sum(array_column($commandesRecommandees, 'prix_estime'));

        return response()->json([
            'success' => true,
            'total_commandes_recommandees' => count($commandesRecommandees),
            'cout_total_estime' => round($coutTotal, 2),
            'commandes' => $commandesRecommandees
        ]);
    }

    /**
     * Trouver le meilleur fournisseur pour un produit
     */
    private function trouverMeilleurFournisseur($produitId)
    {
        // Logique pour trouver le meilleur fournisseur
        // Basée sur les commandes précédentes, l'évaluation, etc.

        $meilleurFournisseur = DB::table('commande_achats')
            ->join('detail_commande_achats', 'commande_achats.id', '=', 'detail_commande_achats.commande_achat_id')
            ->join('fournisseurs', 'commande_achats.fournisseur_id', '=', 'fournisseurs.id')
            ->where('detail_commande_achats.produit_id', $produitId)
            ->where('commande_achats.statut', 'Réceptionnée')
            ->select(
                'fournisseurs.id',
                'fournisseurs.nom',
                'fournisseurs.evaluation',
                DB::raw('COUNT(*) as nombre_commandes'),
                DB::raw('AVG(detail_commande_achats.prix_unitaire) as prix_moyen')
            )
            ->groupBy('fournisseurs.id', 'fournisseurs.nom', 'fournisseurs.evaluation')
            ->orderByDesc('fournisseurs.evaluation')
            ->orderByDesc('nombre_commandes')
            ->first();

        if ($meilleurFournisseur) {
            return [
                'id' => $meilleurFournisseur->id,
                'nom' => $meilleurFournisseur->nom,
                'evaluation' => $meilleurFournisseur->evaluation,
                'prix_moyen' => round($meilleurFournisseur->prix_moyen, 2)
            ];
        }

        return null;
    }

    /**
     * Obtenir les statistiques de consommation par produit
     */
    public function statistiquesConsommation(Request $request)
    {
        $produitId = $request->input('produit_id');
        $periode = $request->input('periode', 90);

        $dateDebut = Carbon::now()->subDays($periode);

        $query = DetailVente::query()
            ->join('ventes', 'detail_ventes.vente_id', '=', 'ventes.id')
            ->join('produits', 'detail_ventes.produit_id', '=', 'produits.id')
            ->where('ventes.date_vente', '>=', $dateDebut)
            ->whereIn('ventes.statut', ['Validée', 'Livrée'])
            ->select(
                'produits.id',
                'produits.code',
                'produits.nom',
                DB::raw('SUM(detail_ventes.quantite) as quantite_totale'),
                DB::raw('COUNT(DISTINCT ventes.id) as nombre_ventes'),
                DB::raw('AVG(detail_ventes.quantite) as quantite_moyenne_par_vente'),
                DB::raw('SUM(detail_ventes.sous_total) as chiffre_affaires')
            )
            ->groupBy('produits.id', 'produits.code', 'produits.nom');

        if ($produitId) {
            $query->where('produits.id', $produitId);
        }

        $statistiques = $query->get();

        return response()->json([
            'success' => true,
            'periode_jours' => $periode,
            'statistiques' => $statistiques
        ]);
    }
}
