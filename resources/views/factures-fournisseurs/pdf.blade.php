<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Fournisseur {{ $facture->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }

        .container { padding: 20px; }

        /* En-tête avec fond rouge pour fournisseurs */
        .header {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            padding: 30px;
            color: white;
            border-radius: 10px 10px 0 0;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-info h1 {
            font-size: 32px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .company-info p {
            font-size: 11px;
            opacity: 0.9;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice-title p {
            font-size: 16px;
            font-weight: 600;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            width: 48%;
            border-left: 4px solid #e74c3c;
        }

        .info-box h3 {
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 8px;
        }

        .status-payee { background: #2ecc71; color: white; }
        .status-impayee { background: #e74c3c; color: white; }
        .status-partielle { background: #f39c12; color: white; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table thead {
            background: #c0392b;
            color: white;
        }

        table thead th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
        }

        table tbody td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 11px;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            float: right;
            border: 2px solid #e74c3c;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 12px;
        }

        .total-row.grand-total {
            border-top: 2px solid #e74c3c;
            margin-top: 10px;
            padding-top: 15px;
        }

        .total-row.grand-total .label {
            font-size: 16px;
            font-weight: bold;
        }

        .total-row.grand-total .amount {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
        }

        .footer {
            clear: both;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-content">
            <div class="company-info">
                <h1>AGROCEAN</h1>
                <p>Gestion & Distribution Agro-alimentaire</p>
                <p>📍 Dakar, Sénégal</p>
                <p>📞 +221 XX XXX XX XX | ✉️ contact@agrocean.sn</p>
            </div>
            <div class="invoice-title">
                <h2>FACTURE FOURNISSEUR</h2>
                <p>{{ $facture->numero }}</p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>FOURNISSEUR</h3>
            <p><strong>{{ $facture->fournisseur->nom }}</strong></p>
            @if($facture->fournisseur->adresse)
                <p>📍 {{ $facture->fournisseur->adresse }}</p>
            @endif
            @if($facture->fournisseur->telephone)
                <p>📞 {{ $facture->fournisseur->telephone }}</p>
            @endif
        </div>

        <div class="info-box">
            <h3>DÉTAILS FACTURE</h3>
            <p><strong>Date d'émission :</strong> {{ date('d/m/Y', strtotime($facture->date_emission)) }}</p>
            <p><strong>Date d'échéance :</strong> {{ date('d/m/Y', strtotime($facture->date_echeance)) }}</p>
            <p><strong>Statut :</strong>
                <span class="status-badge status-{{ strtolower(str_replace(' ', '', $facture->statut)) }}">
                        {{ $facture->statut }}
                    </span>
            </p>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Produit</th>
            <th class="text-center">Quantité</th>
            <th class="text-right">Prix Unitaire</th>
            <th class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($facture->commandeAchat->detailCommandeAchats as $detail)
            <tr>
                <td><strong>{{ $detail->produit->nom }}</strong></td>
                <td class="text-center">{{ $detail->quantite }}</td>
                <td class="text-right">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                <td class="text-right"><strong>{{ number_format($detail->sous_total, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row grand-total">
            <span class="label">TOTAL :</span>
            <span class="amount">{{ number_format($facture->montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
        @if($facture->paiements && $facture->paiements->count() > 0)
            <div class="total-row" style="color: #2ecc71;">
                <span class="label">Montant payé :</span>
                <span class="amount">{{ number_format($facture->paiements->sum('montant'), 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="total-row" style="color: #e74c3c;">
                <span class="label">Montant restant :</span>
                <span class="amount">{{ number_format($facture->montant_total - $facture->paiements->sum('montant'), 0, ',', ' ') }} FCFA</span>
            </div>
        @endif
    </div>

    <div class="footer">
        <p><strong>Merci pour votre collaboration !</strong></p>
        <p>Document généré le {{ date('d/m/Y à H:i') }}</p>
        <p style="margin-top: 15px;">AGROCEAN © {{ date('Y') }}</p>
    </div>
</div>
</body>
</html>
