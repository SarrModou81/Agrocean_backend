<?php

// routes/api.php

use App\Http\Controllers\FactureFournisseurController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\EntrepotController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\CommandeAchatController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\LivraisonController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\BilanFinancierController;
use App\Http\Controllers\PrevisionController;
use App\Http\Controllers\TraceabiliteController;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Routes Protégées
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // ===== AUTHENTIFICATION =====
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    // Routes de profil
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // ===== UTILISATEURS (Administrateur uniquement) =====
    Route::middleware('role:Administrateur')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('/users/{id}/toggle-active', [UserController::class, 'toggleActive']);
        Route::post('/users/{id}/assign-role', [UserController::class, 'assignRole']);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::get('/users/stats/global', [UserController::class, 'statistiques']);
    });

    // ===== CLIENTS (Commercial, Administrateur) =====
    Route::middleware('role:Administrateur,Commercial')->group(function () {
        Route::apiResource('clients', ClientController::class)->except(['destroy']);
        Route::get('/clients/{id}/historique', [ClientController::class, 'historique']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
    });

    // ===== CATÉGORIES (GestionnaireStock, Administrateur) =====
    Route::middleware('role:Administrateur,GestionnaireStock')->group(function () {
        Route::apiResource('categories', CategorieController::class)->except(['destroy']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/categories/{id}', [CategorieController::class, 'destroy']);
    });

    // ===== PRODUITS (GestionnaireStock, Administrateur) =====
    Route::middleware('role:Administrateur,GestionnaireStock,Commercial,Comptable,AgentApprovisionnement')->group(function () {
        Route::get('/produits', [ProduitController::class, 'index']);
        Route::get('/produits/{id}', [ProduitController::class, 'show']);
        Route::get('/produits/verifier/stock', [ProduitController::class, 'verifierStock']);
    });
    Route::middleware('role:Administrateur,GestionnaireStock')->group(function () {
        Route::post('/produits', [ProduitController::class, 'store']);
        Route::put('/produits/{id}', [ProduitController::class, 'update']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/produits/{id}', [ProduitController::class, 'destroy']);
    });

    // ===== ENTREPÔTS (GestionnaireStock, Administrateur) =====
    Route::middleware('role:Administrateur,GestionnaireStock')->group(function () {
        Route::apiResource('entrepots', EntrepotController::class)->except(['destroy']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/entrepots/{id}', [EntrepotController::class, 'destroy']);
    });

    // ===== STOCKS (GestionnaireStock, Administrateur) =====
    Route::middleware('role:Administrateur,GestionnaireStock,Commercial,AgentApprovisionnement')->group(function () {
        Route::get('/stocks', [StockController::class, 'index']);
        Route::get('/stocks/{id}', [StockController::class, 'show']);
        Route::get('/stocks/verifier/peremptions', [StockController::class, 'verifierPeremptions']);
        Route::get('/stocks/inventaire/complet', [StockController::class, 'inventaire']);
        Route::get('/stocks/tracer/{produitId}', [StockController::class, 'tracerProduit']);
        Route::get('/stocks/mouvements/periode', [StockController::class, 'mouvementsPeriode']);
    });
    Route::middleware('role:Administrateur,GestionnaireStock')->group(function () {
        Route::post('/stocks', [StockController::class, 'store']);
        Route::put('/stocks/{id}', [StockController::class, 'update']);
        Route::post('/stocks/{id}/ajuster', [StockController::class, 'ajusterStock']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/stocks/{id}', [StockController::class, 'destroy']);
    });

    // ===== VENTES (Commercial, Administrateur) =====
    Route::middleware('role:Administrateur,Commercial,Comptable,GestionnaireStock')->group(function () {
        Route::get('/ventes', [VenteController::class, 'index']);
        Route::get('/ventes/{id}', [VenteController::class, 'show']);
        Route::get('/ventes/statistiques/analyse', [VenteController::class, 'statistiques']);
    });
    Route::middleware('role:Administrateur,Commercial')->group(function () {
        Route::post('/ventes', [VenteController::class, 'store']);
        Route::put('/ventes/{id}', [VenteController::class, 'update']);
        Route::post('/ventes/{id}/valider', [VenteController::class, 'valider']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/ventes/{id}', [VenteController::class, 'destroy']);
        Route::post('/ventes/{id}/annuler', [VenteController::class, 'annuler']);
    });

    // ===== COMMANDES D'ACHAT (AgentApprovisionnement, Administrateur) =====
    Route::middleware('role:Administrateur,AgentApprovisionnement,Comptable,GestionnaireStock')->group(function () {
        Route::get('/commandes-achat', [CommandeAchatController::class, 'index']);
        Route::get('/commandes-achat/{id}', [CommandeAchatController::class, 'show']);
    });
    Route::middleware('role:Administrateur,AgentApprovisionnement')->group(function () {
        Route::post('/commandes-achat', [CommandeAchatController::class, 'store']);
        Route::put('/commandes-achat/{id}', [CommandeAchatController::class, 'update']);
        Route::post('/commandes-achat/{id}/valider', [CommandeAchatController::class, 'valider']);
        Route::post('/commandes-achat/{id}/receptionner', [CommandeAchatController::class, 'receptionner']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/commandes-achat/{id}', [CommandeAchatController::class, 'destroy']);
        Route::post('/commandes-achat/{id}/annuler', [CommandeAchatController::class, 'annuler']);
    });

    // ===== FOURNISSEURS (AgentApprovisionnement, Administrateur) =====
    Route::middleware('role:Administrateur,AgentApprovisionnement,Comptable,GestionnaireStock')->group(function () {
        Route::get('/fournisseurs', [FournisseurController::class, 'index']);
        Route::get('/fournisseurs/{id}', [FournisseurController::class, 'show']);
        Route::get('/fournisseurs/{id}/historique', [FournisseurController::class, 'historique']);
        Route::get('/fournisseurs/top/meilleurs', [FournisseurController::class, 'topFournisseurs']);
        Route::get('/fournisseurs/recherche/avancee', [FournisseurController::class, 'rechercher']);
    });
    Route::middleware('role:Administrateur,AgentApprovisionnement')->group(function () {
        Route::post('/fournisseurs', [FournisseurController::class, 'store']);
        Route::put('/fournisseurs/{id}', [FournisseurController::class, 'update']);
        Route::post('/fournisseurs/{id}/evaluer', [FournisseurController::class, 'evaluer']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/fournisseurs/{id}', [FournisseurController::class, 'destroy']);
    });

    // ===== LIVRAISONS (Commercial, Administrateur) =====
    Route::middleware('role:Administrateur,Commercial')->group(function () {
        Route::apiResource('livraisons', LivraisonController::class)->except(['destroy']);
        Route::post('/livraisons/{id}/demarrer', [LivraisonController::class, 'demarrer']);
        Route::post('/livraisons/{id}/confirmer', [LivraisonController::class, 'confirmer']);
        Route::get('/livraisons/aujourd-hui/liste', [LivraisonController::class, 'aujourdhui']);
        Route::get('/livraisons/statistiques/analyse', [LivraisonController::class, 'statistiques']);
        Route::get('/livraisons/{id}/bon-livraison', [LivraisonController::class, 'genererBonLivraison']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/livraisons/{id}', [LivraisonController::class, 'destroy']);
        Route::post('/livraisons/{id}/annuler', [LivraisonController::class, 'annuler']);
    });

    // ===== FACTURES (Comptable, Commercial, Administrateur) =====
    Route::middleware('role:Administrateur,Comptable,Commercial')->group(function () {
        Route::apiResource('factures', FactureController::class)->except(['destroy']);
        Route::get('/factures/impayees/liste', [FactureController::class, 'impayees']);
        Route::get('/factures/echues/liste', [FactureController::class, 'echues']);
        Route::get('/factures/{id}/generer-pdf', [FactureController::class, 'genererPDF']);
        Route::post('/factures/{id}/envoyer', [FactureController::class, 'envoyer']);
        Route::get('/factures/statistiques/analyse', [FactureController::class, 'statistiques']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/factures/{id}', [FactureController::class, 'destroy']);
    });

    // ===== PAIEMENTS (Comptable, Administrateur) =====
    Route::middleware('role:Administrateur,Comptable')->group(function () {
        Route::apiResource('paiements', PaiementController::class)->except(['destroy']);
        Route::get('/paiements/statistiques/analyse', [PaiementController::class, 'statistiques']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/paiements/{id}', [PaiementController::class, 'destroy']);
    });

    // ===== ALERTES (Tous les utilisateurs authentifiés) =====
    Route::get('alertes', [AlerteController::class, 'index']);
    Route::get('alertes/non-lues/count', [AlerteController::class, 'getNonLuesCount']);
    Route::post('alertes/{id}/lire', [AlerteController::class, 'marquerLue']);
    Route::post('alertes/tout-lire', [AlerteController::class, 'marquerToutesLues']);
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('alertes/{id}', [AlerteController::class, 'destroy']);
    });

    // ===== RAPPORTS (Tous sauf AgentApprovisionnement) =====
    Route::middleware('role:Administrateur,Commercial,GestionnaireStock,Comptable')->group(function () {
        Route::get('/rapports/dashboard', [RapportController::class, 'dashboard']);
        Route::get('/rapports/stocks', [RapportController::class, 'rapportStocks']);
        Route::get('/rapports/ventes', [RapportController::class, 'rapportVentes']);
        Route::get('/rapports/performances', [RapportController::class, 'analysePerformances']);
    });
    Route::middleware('role:Administrateur,Comptable')->group(function () {
        Route::get('/rapports/financier', [RapportController::class, 'rapportFinancier']);
    });

    // ===== BILANS FINANCIERS (Comptable, Administrateur) =====
    Route::middleware('role:Administrateur,Comptable')->group(function () {
        Route::apiResource('bilans', BilanFinancierController::class)->only(['index', 'show']);
        Route::post('/bilans/generer', [BilanFinancierController::class, 'genererBilan']);
        Route::get('/bilans/tresorerie/etat', [BilanFinancierController::class, 'etatTresorerie']);
        Route::get('/bilans/compte-resultat', [BilanFinancierController::class, 'compteResultat']);
        Route::get('/bilans/bilan-comptable', [BilanFinancierController::class, 'bilanComptable']);
        Route::get('/bilans/dashboard-financier', [BilanFinancierController::class, 'dashboardFinancier']);
    });

    // ===== FACTURES FOURNISSEURS (Comptable, Administrateur, AgentApprovisionnement) =====
    Route::middleware('role:Administrateur,Comptable,AgentApprovisionnement')->group(function () {
        Route::apiResource('factures-fournisseurs', FactureFournisseurController::class)->except(['destroy']);
        Route::get('/factures-fournisseurs/impayees/liste', [FactureFournisseurController::class, 'impayees']);
        Route::get('/factures-fournisseurs/{id}/generer-pdf', [FactureFournisseurController::class, 'genererPDF']);
    });
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('/factures-fournisseurs/{id}', [FactureFournisseurController::class, 'destroy']);
    });

    // ===== PRÉVISIONS DE RÉAPPROVISIONNEMENT (GestionnaireStock, AgentApprovisionnement, Administrateur) =====
    Route::middleware('role:Administrateur,GestionnaireStock,AgentApprovisionnement')->group(function () {
        Route::get('/previsions/reapprovisionnement', [PrevisionController::class, 'previsions']);
        Route::get('/previsions/produit/{produitId}', [PrevisionController::class, 'previsionProduit']);
        Route::get('/previsions/commandes-recommandees', [PrevisionController::class, 'genererCommandesRecommandees']);
        Route::get('/previsions/statistiques-consommation', [PrevisionController::class, 'statistiquesConsommation']);
    });

    // ===== TRAÇABILITÉ (Tous sauf Commercial basique) =====
    Route::middleware('role:Administrateur,GestionnaireStock,Comptable,AgentApprovisionnement')->group(function () {
        Route::get('/traceabilite/lot/{numeroLot}', [TraceabiliteController::class, 'tracerLot']);
        Route::get('/traceabilite/lot/{numeroLot}/pdf', [TraceabiliteController::class, 'genererFicheTraceabilite']);
        Route::get('/traceabilite/produit/{produitId}', [TraceabiliteController::class, 'tracerProduit']);
        Route::get('/traceabilite/lot/{numeroLot}/clients', [TraceabiliteController::class, 'identifierClientsLot']);
        Route::post('/traceabilite/rappel-produit', [TraceabiliteController::class, 'genererDocumentRappel']);
        Route::get('/traceabilite/vente/{venteId}', [TraceabiliteController::class, 'tracerVente']);
        Route::get('/traceabilite/vente/{venteId}/certificat', [TraceabiliteController::class, 'genererCertificatVente']);
        Route::get('/traceabilite/rapport-periode', [TraceabiliteController::class, 'rapportTraceabilitePeriode']);
    });

});
