<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rappel de Produit - Lot {{ $lot->numero_lot }}</title>
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
            border-bottom: 4px solid #e74c3c;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #e74c3c;
            margin: 0;
            font-size: 26px;
            text-transform: uppercase;
        }
        .header .alert-box {
            background-color: #e74c3c;
            color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .entreprise {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #e74c3c;
            color: white;
            padding: 10px 12px;
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
            padding: 8px 10px;
            background-color: #ecf0f1;
            border: 1px solid #bdc3c7;
        }
        .info-value {
            display: table-cell;
            width: 65%;
            padding: 8px 10px;
            border: 1px solid #bdc3c7;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #c0392b;
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
        .warning-box {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
        }
        .danger-box {
            background-color: #f8d7da;
            border-left: 5px solid #e74c3c;
            padding: 15px;
            margin: 15px 0;
        }
        .risk-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .risk-critique {
            background-color: #e74c3c;
            color: white;
        }
        .risk-eleve {
            background-color: #e67e22;
            color: white;
        }
        .risk-moyen {
            background-color: #f39c12;
            color: white;
        }
        .risk-faible {
            background-color: #3498db;
            color: white;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 2px solid #e74c3c;
            padding-top: 15px;
        }
        .actions-list {
            list-style-type: square;
            padding-left: 20px;
            line-height: 2;
        }
        .stats-summary {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="entreprise">
        <strong>{{ $entreprise['nom'] }}</strong><br>
        {{ $entreprise['adresse'] }}<br>
        Tél: {{ $entreprise['telephone'] }} | Email: {{ $entreprise['email'] }}
    </div>

    <div class="header">
        <h1>⚠ AVIS DE RAPPEL DE PRODUIT ⚠</h1>
        <div class="alert-box">
            RAPPEL IMMÉDIAT - ACTION REQUISE
        </div>
        <p style="margin: 5px 0;">Date du rappel: <strong>{{ $date_rappel }}</strong></p>
        <p style="margin: 5px 0;">Numéro de lot concerné: <strong>{{ $lot->numero_lot }}</strong></p>
    </div>

    <!-- NIVEAU DE RISQUE -->
    <div class="section">
        <div class="section-title">NIVEAU DE RISQUE</div>
        <div style="text-align: center; padding: 20px;">
            <span class="risk-badge risk-{{ strtolower($niveau_risque) }}">
                NIVEAU {{ strtoupper($niveau_risque) }}
            </span>
        </div>
    </div>

    <!-- INFORMATIONS DU PRODUIT RAPPELÉ -->
    <div class="section">
        <div class="section-title">INFORMATIONS DU PRODUIT RAPPELÉ</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Numéro de lot</div>
                <div class="info-value"><strong>{{ $lot->numero_lot }}</strong></div>
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
                <div class="info-label">Date d'entrée en stock</div>
                <div class="info-value">{{ $lot->date_entree }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de péremption</div>
                <div class="info-value">{{ $lot->date_peremption ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Quantité initialement distribuée</div>
                <div class="info-value">{{ $lot->quantite_vendue }} unités</div>
            </div>
        </div>
    </div>

    <!-- MOTIF DU RAPPEL -->
    <div class="section">
        <div class="section-title">MOTIF DU RAPPEL</div>
        <div class="danger-box">
            <p style="margin: 0; font-size: 13px; line-height: 1.8;">
                {{ $motif_rappel }}
            </p>
        </div>
    </div>

    <!-- ORIGINE DU LOT -->
    @if($origine)
    <div class="section">
        <div class="section-title">ORIGINE DU LOT</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Fournisseur</div>
                <div class="info-value">{{ $origine->fournisseur }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contact fournisseur</div>
                <div class="info-value">{{ $origine->fournisseur_contact }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">N° Commande d'achat</div>
                <div class="info-value">{{ $origine->numero }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de réception</div>
                <div class="info-value">{{ $origine->date_livraison }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- STATISTIQUES DU RAPPEL -->
    <div class="section">
        <div class="section-title">PORTÉE DU RAPPEL</div>
        <div class="stats-summary">
            <p style="margin: 5px 0;"><strong>Nombre de clients impactés:</strong> {{ $nombre_clients }}</p>
            <p style="margin: 5px 0;"><strong>Quantité totale distribuée:</strong> {{ $lot->quantite_vendue }} unités</p>
            <p style="margin: 5px 0;"><strong>Quantité restante en stock:</strong> {{ $lot->quantite_actuelle }} unités</p>
        </div>
    </div>

    <!-- ACTIONS RECOMMANDÉES -->
    <div class="section">
        <div class="section-title">ACTIONS RECOMMANDÉES</div>
        <div class="warning-box">
            <p style="margin: 0 0 10px 0; font-weight: bold;">Les actions suivantes doivent être entreprises immédiatement:</p>
            <ul class="actions-list">
                @foreach(explode("\n", $actions_recommandees) as $action)
                    @if(trim($action))
                        <li>{{ trim($action) }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    <!-- LISTE DES CLIENTS IMPACTÉS -->
    <div class="section">
        <div class="section-title">CLIENTS IMPACTÉS À CONTACTER</div>
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Contact</th>
                    <th>Adresse</th>
                    <th>Quantité reçue</th>
                    <th>Nombre de ventes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td><strong>{{ $client->nom }}</strong></td>
                    <td>
                        {{ $client->telephone }}<br>
                        {{ $client->email }}
                    </td>
                    <td>{{ $client->adresse }}</td>
                    <td>{{ $client->quantite_totale }}</td>
                    <td>{{ count($client->ventes) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- DÉTAILS DES VENTES PAR CLIENT -->
    <div class="section">
        <div class="section-title">DÉTAILS DES LIVRAISONS</div>
        @foreach($clients as $client)
        <div style="margin-bottom: 20px;">
            <p style="font-weight: bold; margin: 10px 0 5px 0;">{{ $client->nom }}</p>
            <table>
                <thead>
                    <tr>
                        <th>N° Vente</th>
                        <th>Date vente</th>
                        <th>Quantité</th>
                        <th>Adresse de livraison</th>
                        <th>Date livraison</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client->ventes as $vente)
                    <tr>
                        <td>{{ $vente->numero_vente }}</td>
                        <td>{{ $vente->date_vente }}</td>
                        <td>{{ $vente->quantite }}</td>
                        <td>{{ $vente->adresse_livraison }}</td>
                        <td>{{ $vente->date_livraison ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>

    <!-- INSTRUCTIONS FINALES -->
    <div class="section">
        <div class="danger-box">
            <p style="font-weight: bold; margin: 0 0 10px 0;">PROCÉDURE À SUIVRE:</p>
            <ol style="margin: 0; padding-left: 20px; line-height: 2;">
                <li>Contacter immédiatement tous les clients listés ci-dessus</li>
                <li>Informer du rappel et des risques associés</li>
                <li>Organiser le retour ou la destruction du produit</li>
                <li>Documenter toutes les actions entreprises</li>
                <li>Rendre compte à la direction dans les 48 heures</li>
            </ol>
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ $entreprise['nom'] }}</strong></p>
        <p>Document confidentiel - Distribution restreinte</p>
        <p>Pour toute question, contactez: {{ $entreprise['telephone'] }} | {{ $entreprise['email'] }}</p>
    </div>
</body>
</html>
