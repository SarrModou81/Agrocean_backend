<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de Traçabilité - Lot {{ $lot->numero_lot }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            padding: 6px 10px;
            background-color: #ecf0f1;
            border: 1px solid #bdc3c7;
        }
        .info-value {
            display: table-cell;
            width: 65%;
            padding: 6px 10px;
            border: 1px solid #bdc3c7;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #27ae60;
            color: white;
        }
        .badge-warning {
            background-color: #f39c12;
            color: white;
        }
        .badge-danger {
            background-color: #e74c3c;
            color: white;
        }
        .stats-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            padding: 15px;
            margin: 5px;
            background-color: #ecf0f1;
            border-radius: 5px;
        }
        .stats-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stats-label {
            font-size: 11px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FICHE DE TRAÇABILITÉ</h1>
        <p>Lot N° {{ $lot->numero_lot }}</p>
        <p>Généré le {{ $date_generation }}</p>
    </div>

    <!-- INFORMATIONS DU LOT -->
    <div class="section">
        <div class="section-title">INFORMATIONS DU LOT</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Numéro de lot</div>
                <div class="info-value">{{ $lot->numero_lot }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Produit</div>
                <div class="info-value">{{ $lot->produit->nom }} ({{ $lot->produit->code }})</div>
            </div>
            <div class="info-row">
                <div class="info-label">Catégorie</div>
                <div class="info-value">{{ $lot->produit->categorie }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date d'entrée</div>
                <div class="info-value">{{ $lot->date_entree }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de péremption</div>
                <div class="info-value">{{ $lot->date_peremption ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut actuel</div>
                <div class="info-value">
                    @if($lot->statut == 'Disponible')
                        <span class="badge badge-success">{{ $lot->statut }}</span>
                    @elseif($lot->statut == 'Réservé')
                        <span class="badge badge-warning">{{ $lot->statut }}</span>
                    @else
                        <span class="badge badge-danger">{{ $lot->statut }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ORIGINE DU LOT -->
    @if($origine)
    <div class="section">
        <div class="section-title">ORIGINE DU LOT</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Type</div>
                <div class="info-value">{{ $origine->type }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Numéro de commande</div>
                <div class="info-value">{{ $origine->numero }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fournisseur</div>
                <div class="info-value">{{ $origine->fournisseur }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contact fournisseur</div>
                <div class="info-value">{{ $origine->fournisseur_contact }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de commande</div>
                <div class="info-value">{{ $origine->date }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de livraison</div>
                <div class="info-value">{{ $origine->date_livraison }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- STATISTIQUES -->
    <div class="section">
        <div class="section-title">STATISTIQUES DU LOT</div>
        <div style="text-align: center;">
            <div class="stats-box">
                <div class="stats-value">{{ $lot->quantite_initiale }}</div>
                <div class="stats-label">Quantité initiale</div>
            </div>
            <div class="stats-box">
                <div class="stats-value">{{ $lot->quantite_vendue }}</div>
                <div class="stats-label">Quantité vendue</div>
            </div>
            <div class="stats-box">
                <div class="stats-value">{{ $lot->quantite_actuelle }}</div>
                <div class="stats-label">Quantité restante</div>
            </div>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <div class="stats-box">
                <div class="stats-value">{{ $statistiques->nombre_mouvements }}</div>
                <div class="stats-label">Mouvements totaux</div>
            </div>
            <div class="stats-box">
                <div class="stats-value">{{ $statistiques->nombre_ventes }}</div>
                <div class="stats-label">Ventes</div>
            </div>
            <div class="stats-box">
                <div class="stats-value">{{ $statistiques->taux_ecoulement }}%</div>
                <div class="stats-label">Taux d'écoulement</div>
            </div>
        </div>
    </div>

    <!-- HISTORIQUE DES MOUVEMENTS -->
    <div class="section">
        <div class="section-title">HISTORIQUE DES MOUVEMENTS</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Entrepôt</th>
                    <th>Motif</th>
                    <th>Référence</th>
                    <th>Utilisateur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mouvements as $mouvement)
                <tr>
                    <td>{{ $mouvement->date }}</td>
                    <td>{{ $mouvement->type }}</td>
                    <td>{{ $mouvement->quantite }}</td>
                    <td>{{ $mouvement->entrepot }}</td>
                    <td>{{ $mouvement->motif ?? 'N/A' }}</td>
                    <td>{{ $mouvement->reference_type }} #{{ $mouvement->reference_id }}</td>
                    <td>{{ $mouvement->utilisateur }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- VENTES ASSOCIÉES -->
    @if(count($ventes) > 0)
    <div class="section">
        <div class="section-title">VENTES ASSOCIÉES</div>
        <table>
            <thead>
                <tr>
                    <th>N° Vente</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Quantité</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventes as $vente)
                <tr>
                    <td>{{ $vente->numero }}</td>
                    <td>{{ $vente->date }}</td>
                    <td>{{ $vente->client }}</td>
                    <td>{{ $vente->quantite }}</td>
                    <td>{{ $vente->statut }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- EMPLACEMENTS ACTUELS -->
    <div class="section">
        <div class="section-title">EMPLACEMENTS ACTUELS</div>
        <table>
            <thead>
                <tr>
                    <th>Entrepôt</th>
                    <th>Emplacement</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lot->emplacements_actuels as $emplacement)
                <tr>
                    <td>{{ $emplacement->entrepot }}</td>
                    <td>{{ $emplacement->emplacement }}</td>
                    <td>{{ $emplacement->quantite }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p><strong>AGROCEAN</strong> - Système de Gestion des Stocks et Traçabilité</p>
        <p>Document généré automatiquement - Confidentiel</p>
    </div>
</body>
</html>
