<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat de Traçabilité - Vente {{ $vente->numero }}</title>
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
            border: 3px solid #27ae60;
            padding: 20px;
            background-color: #f0f9f4;
        }
        .header h1 {
            color: #27ae60;
            margin: 0;
            font-size: 24px;
        }
        .header .cert-badge {
            display: inline-block;
            background-color: #27ae60;
            color: white;
            padding: 8px 20px;
            margin: 10px 0;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .entreprise {
            text-align: left;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #ecf0f1;
            border-left: 4px solid #27ae60;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #27ae60;
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
            background-color: #27ae60;
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
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 2px solid #27ae60;
            padding-top: 15px;
        }
        .certification {
            margin-top: 30px;
            padding: 20px;
            border: 2px dashed #27ae60;
            background-color: #f0f9f4;
            text-align: center;
        }
        .signature-box {
            margin-top: 40px;
            display: inline-block;
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="entreprise">
        <strong style="font-size: 16px;">{{ $entreprise['nom'] }}</strong><br>
        {{ $entreprise['adresse'] }}<br>
        Tél: {{ $entreprise['telephone'] }} | Email: {{ $entreprise['email'] }}
    </div>

    <div class="header">
        <h1>CERTIFICAT DE TRAÇABILITÉ</h1>
        <div class="cert-badge">✓ PRODUITS TRACÉS ET CERTIFIÉS</div>
        <p style="margin: 10px 0 0 0; font-size: 11px; color: #7f8c8d;">
            Document généré le {{ $date_generation }}
        </p>
    </div>

    <!-- INFORMATIONS DE LA VENTE -->
    <div class="section">
        <div class="section-title">INFORMATIONS DE LA TRANSACTION</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Numéro de vente</div>
                <div class="info-value"><strong>{{ $vente->numero }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de vente</div>
                <div class="info-value">{{ $vente->date }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut</div>
                <div class="info-value">{{ $vente->statut }}</div>
            </div>
        </div>
    </div>

    <!-- INFORMATIONS CLIENT -->
    <div class="section">
        <div class="section-title">CLIENT</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom</div>
                <div class="info-value">{{ $client->nom }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $client->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $client->telephone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse</div>
                <div class="info-value">{{ $client->adresse }}</div>
            </div>
        </div>
    </div>

    <!-- INFORMATIONS DE LIVRAISON -->
    @if($livraison)
    <div class="section">
        <div class="section-title">LIVRAISON</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Date de livraison</div>
                <div class="info-value">{{ $livraison->date }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse de livraison</div>
                <div class="info-value">{{ $livraison->adresse }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Transporteur</div>
                <div class="info-value">{{ $livraison->transporteur ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Numéro de suivi</div>
                <div class="info-value">{{ $livraison->numero_suivi ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- PRODUITS VENDUS ET TRAÇABILITÉ -->
    <div class="section">
        <div class="section-title">PRODUITS ET TRAÇABILITÉ DES LOTS</div>
        @foreach($produits as $produit)
        <div style="margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
            <p style="font-weight: bold; margin: 0 0 10px 0; color: #27ae60;">
                {{ $produit->produit->nom }} ({{ $produit->produit->code }})
            </p>
            <p style="margin: 5px 0;">
                <strong>Quantité vendue:</strong> {{ $produit->quantite_vendue }} unités |
                <strong>Prix unitaire:</strong> {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA
            </p>

            @if(count($produit->lots_utilises) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Numéro de lot</th>
                        <th>Quantité du lot</th>
                        <th>Date d'entrée en stock</th>
                        <th>Date de péremption</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produit->lots_utilises as $lot)
                    <tr>
                        <td><strong>{{ $lot->numero_lot }}</strong></td>
                        <td>{{ $lot->quantite_vendue }}</td>
                        <td>{{ $lot->date_entree ?? 'N/A' }}</td>
                        <td>{{ $lot->date_peremption ?? 'Non périssable' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="font-style: italic; color: #7f8c8d; margin: 10px 0;">
                Aucun lot spécifique tracé pour ce produit
            </p>
            @endif
        </div>
        @endforeach
    </div>

    <!-- CERTIFICATION -->
    <div class="certification">
        <p style="font-weight: bold; font-size: 14px; margin: 0 0 15px 0; color: #27ae60;">
            CERTIFICATION DE TRAÇABILITÉ
        </p>
        <p style="margin: 5px 0; line-height: 1.8;">
            Nous certifions que tous les produits mentionnés dans ce document ont été tracés
            tout au long de la chaîne d'approvisionnement, depuis leur réception du fournisseur
            jusqu'à leur livraison au client final.
        </p>
        <p style="margin: 5px 0; line-height: 1.8;">
            Chaque lot est identifiable de manière unique et son historique complet est
            disponible dans notre système de gestion.
        </p>
        <p style="margin: 15px 0 5px 0; font-size: 11px; color: #7f8c8d;">
            Ce certificat atteste de la conformité aux normes de traçabilité alimentaire
            et peut être présenté aux autorités compétentes.
        </p>
    </div>

    <div style="margin-top: 50px; text-align: center;">
        <div class="signature-box">
            <strong>{{ $entreprise['nom'] }}</strong><br>
            <small>Responsable Qualité</small>
        </div>
        <div class="signature-box" style="margin-left: 8%;">
            <strong>{{ $client->nom }}</strong><br>
            <small>Client</small>
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ $entreprise['nom'] }}</strong> - Système de Traçabilité Certifié</p>
        <p>Document officiel - Valeur légale</p>
        <p>Référence: CERT-{{ $vente->numero }}-{{ date('Ymd') }}</p>
    </div>
</body>
</html>
