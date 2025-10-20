<?php

// app/Http/Controllers/RapportController.php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Stock;
use App\Models\Paiement;
use App\Models\Produit;
use App\Models\Client;
use App\Models\CommandeAchat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RapportController extends Controller
{
    public function dashboard()
    {
        $aujourdhui = Carbon::today();
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        // Ventes du jour
        $ventesJour = Vente::whereDate('date_vente', $aujourdhui)
            ->where('statut', '!=', 'Annulée')
            ->sum('montant_ttc');

        // Ventes du mois
        $ventesMois = Vente::whereBetween('date_vente', [$debutMois, $finMois])
            ->where('statut', '!=', 'Annulée')
            ->sum('montant_ttc');

        // Nombre de commandes en attente
        $commandesEnAttente = Vente::where('statut', 'Validée')->count();

        // Alertes actives
        $alertesActives = \App\Models\Alerte::where('lue', false)->count();

        // Produits en rupture
        $produitsRupture = Produit::whereDoesntHave('stocks', function($query) {
            $query->where('statut', 'Disponible')->where('quantite', '>', 0);
        })->count();

        // Valeur totale du stock
        $valeurStock = Stock::where('statut', 'Disponible')
            ->get()
            ->sum(function($stock) {
                return $stock->calculerValeur();
            });

        // Top 5 produits vendus ce mois
        $topProduits = \App\Models\DetailVente::whereHas('vente', function($query) use ($debutMois, $finMois) {
            $query->whereBetween('date_vente', [$debutMois, $finMois])
                ->where('statut', '!=', 'Annulée');
        })
            ->with('produit')
            ->selectRaw('produit_id, SUM(quantite) as total_quantite, SUM(sous_total) as total_montant')
            ->groupBy('produit_id')
            ->orderByDesc('total_quantite')
            ->limit(5)
            ->get();

        // Évolution des ventes (7 derniers jours)
        $evolutionVentes = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $montant = Vente::whereDate('date_vente', $date)
                ->where('statut', '!=', 'Annulée')
                ->sum('montant_ttc');

            $evolutionVentes[] = [
                'date' => $date->format('Y-m-d'),
                'montant' => $montant
            ];
        }

        return response()->json([
            'ventes_jour' => $ventesJour,
            'ventes_mois' => $ventesMois,
            'commandes_attente' => $commandesEnAttente,
            'alertes_actives' => $alertesActives,
            'produits_rupture' => $produitsRupture,
            'valeur_stock' => $valeurStock,
            'top_produits' => $topProduits,
            'evolution_ventes' => $evolutionVentes
        ]);
    }

    public function rapportFinancier(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::now()->startOfMonth());
        $dateFin = $request->input('date_fin', Carbon::now()->endOfMonth());

        // Chiffre d'affaires
        $chiffreAffaires = Vente::whereBetween('date_vente', [$dateDebut, $dateFin])
            ->where('statut', '!=', 'Annulée')
            ->sum('montant_ttc');

        // Bénéfices (CA - Coût d'achat)
        $ventes = Vente::with('detailVentes.produit')
            ->whereBetween('date_vente', [$dateDebut, $dateFin])
            ->where('statut', '!=', 'Annulée')
            ->get();

        $coutAchat = 0;
        $margeGlobale = 0;

        foreach ($ventes as $vente) {
            foreach ($vente->detailVentes as $detail) {
                $coutAchat += $detail->quantite * $detail->produit->prix_achat;
                $margeGlobale += $detail->quantite * ($detail->produit->prix_vente - $detail->produit->prix_achat);
            }
        }

        // Charges (commandes d'achat)
        $chargesExploitation = CommandeAchat::whereBetween('date_commande', [$dateDebut, $dateFin])
            ->where('statut', 'Reçue')
            ->sum('montant_total');

        // Créances clients
        $creances = Paiement::whereHas('facture')
            ->whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->sum('montant');

        // Trésorerie
        $tresorerie = Paiement::whereBetween('date_paiement', [$dateDebut, $dateFin])
            ->sum('montant');

        // Évolution mensuelle
        $evolutionMensuelle = [];
        $debut = Carbon::parse($dateDebut);
        $fin = Carbon::parse($dateFin);

        while ($debut <= $fin) {
            $mois = $debut->format('Y-m');
            $ca = Vente::whereYear('date_vente', $debut->year)
                ->whereMonth('date_vente', $debut->month)
                ->where('statut', '!=', 'Annulée')
                ->sum('montant_ttc');

            $evolutionMensuelle[] = [
                'mois' => $mois,
                'chiffre_affaires' => $ca
            ];

            $debut->addMonth();
        }

        return response()->json([
            'periode' => [
                'debut' => $dateDebut,
                'fin' => $dateFin
            ],
            'chiffre_affaires' => $chiffreAffaires,
            'cout_achat' => $coutAchat,
            'marge_globale' => $margeGlobale,
            'charges_exploitation' => $chargesExploitation,
            'benefice_net' => $margeGlobale - $chargesExploitation,
            'creances' => $creances,
            'tresorerie' => $tresorerie,
            'evolution_mensuelle' => $evolutionMensuelle
        ]);
    }

    public function rapportStocks(Request $request)
    {
        $entrepotId = $request->input('entrepot_id');

        $query = Stock::with(['produit', 'entrepot'])
            ->where('statut', 'Disponible');

        if ($entrepotId) {
            $query->where('entrepot_id', $entrepotId);
        }

        $stocks = $query->get();

        // Valeur totale
        $valeurTotale = $stocks->sum(function($stock) {
            return $stock->calculerValeur();
        });

        // Stock par catégorie
        $stockParCategorie = $stocks->groupBy(function($stock) {
            return $stock->produit->categorie->nom;
        })->map(function($items) {
            return [
                'quantite' => $items->sum('quantite'),
                'valeur' => $items->sum(function($stock) {
                    return $stock->calculerValeur();
                })
            ];
        });

        // Stock par entrepôt
        $stockParEntrepot = $stocks->groupBy('entrepot_id')->map(function($items) {
            return [
                'entrepot' => $items->first()->entrepot->nom,
                'quantite' => $items->sum('quantite'),
                'valeur' => $items->sum(function($stock) {
                    return $stock->calculerValeur();
                }),
                'capacite_utilisee' => ($items->sum('quantite') / $items->first()->entrepot->capacite) * 100
            ];
        });

        // Produits à faible stock
        $produitsAlerte = Produit::all()->filter(function($produit) {
            return $produit->stockTotal() > 0 && $produit->stockTotal() < $produit->seuil_minimum;
        })->map(function($produit) {
            return [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'stock_actuel' => $produit->stockTotal(),
                'seuil_minimum' => $produit->seuil_minimum
            ];
        })->values();

        // Produits en rupture
        $produitsRupture = Produit::whereDoesntHave('stocks', function($query) {
            $query->where('statut', 'Disponible')->where('quantite', '>', 0);
        })->get(['id', 'nom', 'code']);

        // Produits proches de la péremption
        $produitsPeremption = Stock::where('statut', 'Disponible')
            ->whereNotNull('date_peremption')
            ->where('date_peremption', '<=', Carbon::now()->addDays(7))
            ->with('produit')
            ->get()
            ->map(function($stock) {
                return [
                    'produit' => $stock->produit->nom,
                    'quantite' => $stock->quantite,
                    'lot' => $stock->numero_lot,
                    'date_peremption' => $stock->date_peremption,
                    'jours_restants' => Carbon::now()->diffInDays($stock->date_peremption, false)
                ];
            });

        return response()->json([
            'valeur_totale' => $valeurTotale,
            'nombre_produits' => $stocks->count(),
            'stock_par_categorie' => $stockParCategorie,
            'stock_par_entrepot' => $stockParEntrepot,
            'produits_alerte' => $produitsAlerte,
            'produits_rupture' => $produitsRupture,
            'produits_peremption' => $produitsPeremption
        ]);
    }

    public function rapportVentes(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::now()->startOfMonth());
        $dateFin = $request->input('date_fin', Carbon::now()->endOfMonth());

        $ventes = Vente::with(['client', 'detailVentes.produit'])
            ->whereBetween('date_vente', [$dateDebut, $dateFin])
            ->where('statut', '!=', 'Annulée')
            ->get();

        // Statistiques globales
        $totalVentes = $ventes->count();
        $montantTotal = $ventes->sum('montant_ttc');
        $panierMoyen = $totalVentes > 0 ? $montantTotal / $totalVentes : 0;

        // Ventes par client
        $ventesParClient = $ventes->groupBy('client_id')->map(function($ventesClient) {
            return [
                'client' => $ventesClient->first()->client->nom,
                'nombre_ventes' => $ventesClient->count(),
                'montant_total' => $ventesClient->sum('montant_ttc'),
                'panier_moyen' => $ventesClient->avg('montant_ttc')
            ];
        })->sortByDesc('montant_total')->take(10)->values();

        // Ventes par produit
        $detailsVentes = \App\Models\DetailVente::whereHas('vente', function($query) use ($dateDebut, $dateFin) {
            $query->whereBetween('date_vente', [$dateDebut, $dateFin])
                ->where('statut', '!=', 'Annulée');
        })
            ->with('produit')
            ->selectRaw('produit_id, SUM(quantite) as quantite_vendue, SUM(sous_total) as chiffre_affaires')
            ->groupBy('produit_id')
            ->get();

        $ventesParProduit = $detailsVentes->map(function($detail) {
            $produit = Produit::find($detail->produit_id);
            return [
                'produit' => $produit->nom,
                'quantite_vendue' => $detail->quantite_vendue,
                'chiffre_affaires' => $detail->chiffre_affaires,
                'marge' => $detail->quantite_vendue * $produit->calculerMarge()
            ];
        })->sortByDesc('chiffre_affaires')->take(10)->values();

        // Tendances (par jour/semaine/mois)
        $tendances = $ventes->groupBy(function($vente) {
            return $vente->date_vente->format('Y-m-d');
        })->map(function($ventesJour) {
            return [
                'nombre' => $ventesJour->count(),
                'montant' => $ventesJour->sum('montant_ttc')
            ];
        });

        // Analyse des marges
        $margesProduits = $detailsVentes->map(function($detail) {
            $produit = Produit::find($detail->produit_id);
            return [
                'produit' => $produit->nom,
                'marge_unitaire' => $produit->calculerMarge(),
                'marge_totale' => $detail->quantite_vendue * $produit->calculerMarge(),
                'taux_marge' => $produit->prix_achat > 0 ?
                    (($produit->prix_vente - $produit->prix_achat) / $produit->prix_achat) * 100 : 0
            ];
        })->sortByDesc('marge_totale')->take(10)->values();

        return response()->json([
            'periode' => [
                'debut' => $dateDebut,
                'fin' => $dateFin
            ],
            'statistiques' => [
                'total_ventes' => $totalVentes,
                'montant_total' => $montantTotal,
                'panier_moyen' => $panierMoyen
            ],
            'ventes_par_client' => $ventesParClient,
            'ventes_par_produit' => $ventesParProduit,
            'tendances' => $tendances,
            'analyse_marges' => $margesProduits
        ]);
    }

    public function analysePerformances(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::now()->startOfYear());
        $dateFin = $request->input('date_fin', Carbon::now());

        // Taux de rotation des stocks
        $produitsAvecRotation = Produit::with('stocks')->get()->map(function($produit) use ($dateDebut, $dateFin) {
            $stockMoyen = $produit->stocks()->avg('quantite') ?? 0;
            $quantiteVendue = \App\Models\DetailVente::where('produit_id', $produit->id)
                ->whereHas('vente', function($query) use ($dateDebut, $dateFin) {
                    $query->whereBetween('date_vente', [$dateDebut, $dateFin])
                        ->where('statut', '!=', 'Annulée');
                })
                ->sum('quantite');

            $tauxRotation = $stockMoyen > 0 ? $quantiteVendue / $stockMoyen : 0;

            return [
                'produit' => $produit->nom,
                'stock_moyen' => $stockMoyen,
                'quantite_vendue' => $quantiteVendue,
                'taux_rotation' => round($tauxRotation, 2)
            ];
        })->sortByDesc('taux_rotation')->take(20)->values();

        // Performance des commerciaux
        $performanceCommerciaux = \App\Models\User::where('role', 'Commercial')
            ->with(['ventes' => function($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_vente', [$dateDebut, $dateFin])
                    ->where('statut', '!=', 'Annulée');
            }])
            ->get()
            ->map(function($user) {
                return [
                    'commercial' => $user->nom . ' ' . $user->prenom,
                    'nombre_ventes' => $user->ventes->count(),
                    'chiffre_affaires' => $user->ventes->sum('montant_ttc'),
                    'panier_moyen' => $user->ventes->count() > 0 ?
                        $user->ventes->sum('montant_ttc') / $user->ventes->count() : 0
                ];
            })
            ->sortByDesc('chiffre_affaires')
            ->values();

        // Analyse des fournisseurs
        $performanceFournisseurs = \App\Models\Fournisseur::with('commandeAchats')
            ->get()
            ->map(function($fournisseur) {
                return [
                    'fournisseur' => $fournisseur->nom,
                    'nombre_commandes' => $fournisseur->commandeAchats->count(),
                    'montant_total' => $fournisseur->commandeAchats->sum('montant_total'),
                    'evaluation' => $fournisseur->evaluer()
                ];
            })
            ->sortByDesc('montant_total')
            ->take(10)
            ->values();

        // Taux de satisfaction (basé sur les retours/annulations)
        $totalVentes = Vente::whereBetween('date_vente', [$dateDebut, $dateFin])->count();
        $ventesAnnulees = Vente::whereBetween('date_vente', [$dateDebut, $dateFin])
            ->where('statut', 'Annulée')
            ->count();

        $tauxSatisfaction = $totalVentes > 0 ?
            (($totalVentes - $ventesAnnulees) / $totalVentes) * 100 : 100;

        return response()->json([
            'rotation_stocks' => $produitsAvecRotation,
            'performance_commerciaux' => $performanceCommerciaux,
            'performance_fournisseurs' => $performanceFournisseurs,
            'taux_satisfaction' => round($tauxSatisfaction, 2),
            'total_ventes' => $totalVentes,
            'ventes_annulees' => $ventesAnnulees
        ]);
    }
}
