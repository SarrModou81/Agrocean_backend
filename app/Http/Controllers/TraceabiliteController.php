<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Produit;
use App\Models\MouvementStock;
use App\Models\Vente;
use App\Models\CommandeAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TraceabiliteController extends Controller
{
    /**
     * Tracer un lot spécifique à travers toute la chaîne
     */
    public function tracerLot($numeroLot)
    {
        // 1. Informations du lot
        $stocks = Stock::where('numero_lot', $numeroLot)
            ->with(['produit', 'entrepot'])
            ->get();

        if ($stocks->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Lot non trouvé'
            ], 404);
        }

        $stock = $stocks->first();

        // 2. Tous les mouvements du lot
        $mouvements = MouvementStock::where('numero_lot', $numeroLot)
            ->with(['produit', 'entrepot', 'user'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($mouvement) {
                return [
                    'id' => $mouvement->id,
                    'type' => $mouvement->type,
                    'quantite' => $mouvement->quantite,
                    'date' => $mouvement->created_at->format('Y-m-d H:i:s'),
                    'entrepot' => $mouvement->entrepot->nom ?? 'N/A',
                    'motif' => $mouvement->motif,
                    'reference_type' => $mouvement->reference_type,
                    'reference_id' => $mouvement->reference_id,
                    'utilisateur' => $mouvement->user ? $mouvement->user->nom . ' ' . $mouvement->user->prenom : 'Système'
                ];
            });

        // 3. Commande d'achat d'origine
        $origine = null;
        $mouvementEntree = $mouvements->where('type', 'Entrée')->first();
        if ($mouvementEntree && $mouvementEntree['reference_type'] === 'CommandeAchat') {
            $commande = CommandeAchat::with('fournisseur')->find($mouvementEntree['reference_id']);
            if ($commande) {
                $origine = [
                    'type' => 'Commande d\'achat',
                    'numero' => $commande->numero,
                    'date' => $commande->date_commande,
                    'fournisseur' => $commande->fournisseur->nom ?? 'N/A',
                    'fournisseur_contact' => $commande->fournisseur->contact ?? 'N/A',
                    'date_livraison' => $commande->date_livraison_prevue
                ];
            }
        }

        // 4. Ventes liées au lot
        $ventes = [];
        $mouvementsSorties = $mouvements->where('type', 'Sortie');
        foreach ($mouvementsSorties as $sortie) {
            if ($sortie['reference_type'] === 'Vente') {
                $vente = Vente::with('client')->find($sortie['reference_id']);
                if ($vente) {
                    $ventes[] = [
                        'numero' => $vente->numero,
                        'date' => $vente->date_vente,
                        'client' => $vente->client->nom ?? 'N/A',
                        'quantite' => $sortie['quantite'],
                        'statut' => $vente->statut
                    ];
                }
            }
        }

        // 5. État actuel du lot
        $quantiteActuelle = $stocks->sum('quantite');
        $quantiteTotale = $mouvements->where('type', 'Entrée')->sum('quantite');
        $quantiteVendue = $mouvements->where('type', 'Sortie')->sum('quantite');

        return response()->json([
            'success' => true,
            'lot' => [
                'numero_lot' => $numeroLot,
                'produit' => [
                    'code' => $stock->produit->code,
                    'nom' => $stock->produit->nom,
                    'categorie' => $stock->produit->categorie->nom ?? 'N/A'
                ],
                'date_entree' => $stock->date_entree,
                'date_peremption' => $stock->date_peremption,
                'statut' => $stock->statut,
                'quantite_initiale' => $quantiteTotale,
                'quantite_vendue' => $quantiteVendue,
                'quantite_actuelle' => $quantiteActuelle,
                'emplacements_actuels' => $stocks->map(function($s) {
                    return [
                        'entrepot' => $s->entrepot->nom,
                        'emplacement' => $s->emplacement,
                        'quantite' => $s->quantite
                    ];
                })
            ],
            'origine' => $origine,
            'mouvements' => $mouvements->values(),
            'ventes' => $ventes,
            'statistiques' => [
                'nombre_mouvements' => $mouvements->count(),
                'nombre_ventes' => count($ventes),
                'taux_ecoulement' => $quantiteTotale > 0 ? round(($quantiteVendue / $quantiteTotale) * 100, 2) : 0
            ]
        ]);
    }

    /**
     * Générer une fiche de traçabilité réglementaire (PDF)
     */
    public function genererFicheTraceabilite($numeroLot)
    {
        $traceabilite = $this->tracerLot($numeroLot);

        if (!$traceabilite->getData()->success) {
            return $traceabilite;
        }

        $data = $traceabilite->getData();

        $pdf = PDF::loadView('pdf.fiche_traceabilite', [
            'lot' => $data->lot,
            'origine' => $data->origine,
            'mouvements' => $data->mouvements,
            'ventes' => $data->ventes,
            'statistiques' => $data->statistiques,
            'date_generation' => Carbon::now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->download("fiche_traceabilite_{$numeroLot}.pdf");
    }

    /**
     * Tracer tous les lots d'un produit
     */
    public function tracerProduit($produitId)
    {
        $produit = Produit::with('categorie')->findOrFail($produitId);

        $lots = Stock::where('produit_id', $produitId)
            ->select('numero_lot')
            ->distinct()
            ->get()
            ->pluck('numero_lot');

        $informationsLots = [];

        foreach ($lots as $numeroLot) {
            $stocks = Stock::where('numero_lot', $numeroLot)
                ->where('produit_id', $produitId)
                ->get();

            $quantiteActuelle = $stocks->sum('quantite');
            $mouvements = MouvementStock::where('numero_lot', $numeroLot)->count();

            $informationsLots[] = [
                'numero_lot' => $numeroLot,
                'quantite_actuelle' => $quantiteActuelle,
                'date_entree' => $stocks->first()->date_entree,
                'date_peremption' => $stocks->first()->date_peremption,
                'statut' => $stocks->first()->statut,
                'nombre_mouvements' => $mouvements,
                'emplacements' => $stocks->count()
            ];
        }

        return response()->json([
            'success' => true,
            'produit' => [
                'id' => $produit->id,
                'code' => $produit->code,
                'nom' => $produit->nom,
                'categorie' => $produit->categorie->nom ?? 'N/A'
            ],
            'nombre_lots' => count($lots),
            'lots' => $informationsLots
        ]);
    }

    /**
     * Identifier les clients ayant reçu un lot spécifique (pour rappels produits)
     */
    public function identifierClientsLot($numeroLot)
    {
        $mouvements = MouvementStock::where('numero_lot', $numeroLot)
            ->where('type', 'Sortie')
            ->where('reference_type', 'Vente')
            ->get();

        $clients = [];
        $clientsUniques = [];

        foreach ($mouvements as $mouvement) {
            $vente = Vente::with('client', 'livraison')->find($mouvement->reference_id);

            if ($vente && $vente->client) {
                $clientId = $vente->client->id;

                if (!isset($clientsUniques[$clientId])) {
                    $clientsUniques[$clientId] = [
                        'client_id' => $vente->client->id,
                        'nom' => $vente->client->nom,
                        'email' => $vente->client->email,
                        'telephone' => $vente->client->telephone,
                        'adresse' => $vente->client->adresse,
                        'ventes' => [],
                        'quantite_totale' => 0
                    ];
                }

                $clientsUniques[$clientId]['ventes'][] = [
                    'numero_vente' => $vente->numero,
                    'date_vente' => $vente->date_vente,
                    'quantite' => $mouvement->quantite,
                    'adresse_livraison' => $vente->livraison->adresse_livraison ?? 'N/A',
                    'date_livraison' => $vente->livraison->date_livraison ?? null
                ];

                $clientsUniques[$clientId]['quantite_totale'] += $mouvement->quantite;
            }
        }

        return response()->json([
            'success' => true,
            'numero_lot' => $numeroLot,
            'nombre_clients_impactes' => count($clientsUniques),
            'clients' => array_values($clientsUniques)
        ]);
    }

    /**
     * Générer un document de rappel de produit
     */
    public function genererDocumentRappel(Request $request)
    {
        $request->validate([
            'numero_lot' => 'required|string',
            'motif_rappel' => 'required|string',
            'niveau_risque' => 'required|in:Faible,Moyen,Élevé,Critique',
            'actions_recommandees' => 'required|string'
        ]);

        $numeroLot = $request->numero_lot;
        $traceLot = $this->tracerLot($numeroLot);

        if (!$traceLot->getData()->success) {
            return $traceLot;
        }

        $clients = $this->identifierClientsLot($numeroLot);
        $dataLot = $traceLot->getData();
        $dataClients = $clients->getData();

        $pdf = PDF::loadView('pdf.rappel_produit', [
            'lot' => $dataLot->lot,
            'origine' => $dataLot->origine,
            'clients' => $dataClients->clients,
            'nombre_clients' => $dataClients->nombre_clients_impactes,
            'motif_rappel' => $request->motif_rappel,
            'niveau_risque' => $request->niveau_risque,
            'actions_recommandees' => $request->actions_recommandees,
            'date_rappel' => Carbon::now()->format('d/m/Y'),
            'entreprise' => [
                'nom' => 'AGROCEAN',
                'adresse' => 'Dakar, Sénégal',
                'telephone' => '+221 XX XXX XX XX',
                'email' => 'contact@agrocean.sn'
            ]
        ]);

        return $pdf->download("rappel_produit_{$numeroLot}_" . date('Ymd') . ".pdf");
    }

    /**
     * Obtenir l'historique complet d'un produit vendu (traçabilité aval)
     */
    public function tracerVente($venteId)
    {
        $vente = Vente::with(['client', 'detailVentes.produit', 'livraison'])
            ->findOrFail($venteId);

        $produitsTraces = [];

        foreach ($vente->detailVentes as $detail) {
            // Trouver les mouvements de sortie liés à cette vente pour ce produit
            $mouvements = MouvementStock::where('reference_type', 'Vente')
                ->where('reference_id', $vente->id)
                ->where('produit_id', $detail->produit_id)
                ->with('stock')
                ->get();

            $lots = [];
            foreach ($mouvements as $mouvement) {
                if ($mouvement->numero_lot) {
                    // Tracer l'origine du lot
                    $stock = Stock::where('numero_lot', $mouvement->numero_lot)
                        ->where('produit_id', $detail->produit_id)
                        ->first();

                    $lots[] = [
                        'numero_lot' => $mouvement->numero_lot,
                        'quantite_vendue' => $mouvement->quantite,
                        'date_entree' => $stock->date_entree ?? null,
                        'date_peremption' => $stock->date_peremption ?? null
                    ];
                }
            }

            $produitsTraces[] = [
                'produit' => [
                    'code' => $detail->produit->code,
                    'nom' => $detail->produit->nom
                ],
                'quantite_vendue' => $detail->quantite,
                'prix_unitaire' => $detail->prix_unitaire,
                'lots_utilises' => $lots
            ];
        }

        return response()->json([
            'success' => true,
            'vente' => [
                'numero' => $vente->numero,
                'date' => $vente->date_vente,
                'statut' => $vente->statut
            ],
            'client' => [
                'nom' => $vente->client->nom,
                'email' => $vente->client->email,
                'telephone' => $vente->client->telephone,
                'adresse' => $vente->client->adresse
            ],
            'livraison' => $vente->livraison ? [
                'date' => $vente->livraison->date_livraison,
                'adresse' => $vente->livraison->adresse_livraison,
                'transporteur' => $vente->livraison->transporteur,
                'numero_suivi' => $vente->livraison->numero_suivi
            ] : null,
            'produits' => $produitsTraces
        ]);
    }

    /**
     * Générer un certificat de traçabilité pour une vente
     */
    public function genererCertificatVente($venteId)
    {
        $traceVente = $this->tracerVente($venteId);

        if (!$traceVente->getData()->success) {
            return $traceVente;
        }

        $data = $traceVente->getData();

        $pdf = PDF::loadView('pdf.certificat_traceabilite', [
            'vente' => $data->vente,
            'client' => $data->client,
            'livraison' => $data->livraison,
            'produits' => $data->produits,
            'date_generation' => Carbon::now()->format('d/m/Y H:i:s'),
            'entreprise' => [
                'nom' => 'AGROCEAN',
                'adresse' => 'Dakar, Sénégal',
                'telephone' => '+221 XX XXX XX XX',
                'email' => 'contact@agrocean.sn'
            ]
        ]);

        return $pdf->download("certificat_traceabilite_vente_{$data->vente->numero}.pdf");
    }

    /**
     * Rapport de traçabilité par période
     */
    public function rapportTraceabilitePeriode(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut'
        ]);

        $dateDebut = Carbon::parse($request->date_debut);
        $dateFin = Carbon::parse($request->date_fin);

        $mouvements = MouvementStock::whereBetween('created_at', [$dateDebut, $dateFin])
            ->with(['produit', 'entrepot', 'user'])
            ->get();

        $statistiques = [
            'total_mouvements' => $mouvements->count(),
            'entrees' => $mouvements->where('type', 'Entrée')->count(),
            'sorties' => $mouvements->where('type', 'Sortie')->count(),
            'ajustements' => $mouvements->where('type', 'Ajustement')->count(),
            'quantite_entree' => $mouvements->where('type', 'Entrée')->sum('quantite'),
            'quantite_sortie' => $mouvements->where('type', 'Sortie')->sum('quantite'),
            'lots_uniques' => $mouvements->pluck('numero_lot')->unique()->count(),
            'produits_concernes' => $mouvements->pluck('produit_id')->unique()->count()
        ];

        return response()->json([
            'success' => true,
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d'),
                'jours' => $dateDebut->diffInDays($dateFin) + 1
            ],
            'statistiques' => $statistiques,
            'mouvements' => $mouvements->map(function($m) {
                return [
                    'id' => $m->id,
                    'type' => $m->type,
                    'produit' => $m->produit->nom,
                    'numero_lot' => $m->numero_lot,
                    'quantite' => $m->quantite,
                    'entrepot' => $m->entrepot->nom ?? 'N/A',
                    'date' => $m->created_at->format('Y-m-d H:i:s'),
                    'utilisateur' => $m->user ? $m->user->nom . ' ' . $m->user->prenom : 'Système'
                ];
            })
        ]);
    }
}
