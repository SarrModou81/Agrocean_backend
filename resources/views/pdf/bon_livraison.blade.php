<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Livraison - {{ $livraison->vente->numero }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .company-info {
            font-weight: bold;
            font-size: 18px;
            color: #2c3e50;
        }
        .document-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
            margin: 20px 0;
            padding: 15px;
            background-color: #ecf0f1;
            border-left: 5px solid #3498db;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            font-size: 13px;
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
            width: 30%;
            font-weight: bold;
            padding: 6px 10px;
            background-color: #ecf0f1;
            border: 1px solid #bdc3c7;
        }
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 6px 10px;
            border: 1px solid #bdc3c7;
        }
        table.products {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.products th {
            background-color: #34495e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
        }
        table.products td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table.products tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-line {
            padding: 5px 0;
            font-size: 13px;
        }
        .total-line.final {
            font-size: 16px;
            font-weight: bold;
            color: #3498db;
            border-top: 2px solid #3498db;
            padding-top: 10px;
        }
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 48%;
            border: 1px solid #bdc3c7;
            padding: 15px;
            text-align: center;
        }
        .signature-box.right {
            margin-left: 4%;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 15px;
        }
        .barcode {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 3px;
            padding: 10px;
            border: 2px solid #3498db;
            background-color: #fff;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="company-info">{{ $entreprise['nom'] }}</div>
            <p style="margin: 5px 0;">{{ $entreprise['adresse'] }}</p>
            <p style="margin: 5px 0;">Tél: {{ $entreprise['telephone'] }}</p>
            <p style="margin: 5px 0;">Email: {{ $entreprise['email'] }}</p>
        </div>
        <div class="header-right">
            <div class="barcode">
                BL-{{ $livraison->id }}
            </div>
            <p style="margin: 5px 0;"><strong>Date:</strong> {{ $date_generation }}</p>
            @if($livraison->numero_suivi)
            <p style="margin: 5px 0;"><strong>N° Suivi:</strong> {{ $livraison->numero_suivi }}</p>
            @endif
        </div>
    </div>

    <div class="document-title">
        BON DE LIVRAISON N° BL-{{ str_pad($livraison->id, 6, '0', STR_PAD_LEFT) }}
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
    <div class="section">
        <div class="section-title">DÉTAILS DE LA LIVRAISON</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">N° de vente</div>
                <div class="info-value">{{ $vente->numero }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de vente</div>
                <div class="info-value">{{ $vente->date_vente }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de livraison prévue</div>
                <div class="info-value">{{ $livraison->date_livraison ?? 'À définir' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse de livraison</div>
                <div class="info-value">{{ $livraison->adresse_livraison }}</div>
            </div>
            @if($livraison->transporteur)
            <div class="info-row">
                <div class="info-label">Transporteur</div>
                <div class="info-value">{{ $livraison->transporteur }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Statut</div>
                <div class="info-value"><strong>{{ $livraison->statut }}</strong></div>
            </div>
        </div>
    </div>

    <!-- PRODUITS -->
    <div class="section">
        <div class="section-title">PRODUITS À LIVRER</div>
        <table class="products">
            <thead>
                <tr>
                    <th style="width: 10%;">Code</th>
                    <th style="width: 40%;">Désignation</th>
                    <th style="width: 15%; text-align: center;">Quantité</th>
                    <th style="width: 15%; text-align: right;">Prix unitaire</th>
                    <th style="width: 20%; text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produits as $produit)
                <tr>
                    <td>{{ $produit['code'] }}</td>
                    <td>{{ $produit['nom'] }}</td>
                    <td style="text-align: center;"><strong>{{ $produit['quantite'] }}</strong></td>
                    <td style="text-align: right;">{{ number_format($produit['prix_unitaire'], 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right;">{{ number_format($produit['sous_total'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- TOTAUX -->
    <div class="total-section">
        <div class="total-line">
            <strong>Montant HT:</strong> {{ number_format($vente->montant_ht, 0, ',', ' ') }} FCFA
        </div>
        @if($vente->remise > 0)
        <div class="total-line" style="color: #27ae60;">
            <strong>Remise:</strong> - {{ number_format($vente->remise, 0, ',', ' ') }} FCFA
        </div>
        @endif
        <div class="total-line">
            <strong>TVA (18%):</strong> {{ number_format($vente->montant_ttc - $vente->montant_ht, 0, ',', ' ') }} FCFA
        </div>
        <div class="total-line final">
            <strong>TOTAL TTC:</strong> {{ number_format($vente->montant_ttc, 0, ',', ' ') }} FCFA
        </div>
    </div>

    <!-- SIGNATURES -->
    <div class="signature-section">
        <div class="signature-box">
            <p style="font-weight: bold; margin: 0 0 50px 0;">Signature du livreur</p>
            <p style="margin: 0; font-size: 10px;">Nom et date</p>
        </div>
        <div class="signature-box right">
            <p style="font-weight: bold; margin: 0 0 50px 0;">Signature du client</p>
            <p style="margin: 0; font-size: 10px;">Nom et date</p>
        </div>
    </div>

    <div style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
        <p style="margin: 0; font-size: 11px;">
            <strong>Remarques importantes:</strong><br>
            - Vérifier la conformité des produits à la livraison<br>
            - Signaler toute anomalie immédiatement<br>
            - Conserver ce bon de livraison pour toute réclamation
        </p>
    </div>

    <div class="footer">
        <p><strong>{{ $entreprise['nom'] }}</strong> - Merci de votre confiance</p>
        <p>Document généré automatiquement le {{ $date_generation }}</p>
    </div>
</body>
</html>
