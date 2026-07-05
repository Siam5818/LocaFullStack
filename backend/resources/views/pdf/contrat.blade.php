<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Contrat #{{ $contrat->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            margin: 40px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #0f3d3e;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 20px;
            color: #0f3d3e;
            margin: 0;
        }

        .header p {
            color: #555;
            margin: 4px 0 0;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f3d3e;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .label {
            color: #666;
            width: 40%;
        }

        .value {
            font-weight: bold;
        }

        .montant-box {
            background: #f4f7f6;
            border: 1px solid #d8e3e1;
            border-radius: 6px;
            padding: 15px;
            margin-top: 10px;
        }

        .montant-box .total {
            font-size: 16px;
            color: #0f3d3e;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .signatures {
            margin-top: 50px;
            display: flex;
        }

        .signature-box {
            width: 45%;
            display: inline-block;
            text-align: center;
            border-top: 1px solid #999;
            padding-top: 8px;
            margin-right: 5%;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>CONTRAT N° {{ str_pad($contrat->id, 6, '0', STR_PAD_LEFT) }}</h1>
        <p>{{ $contrat->typeContrat->nom }} — Signé le {{ $contrat->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Parties au contrat</div>
        <table>
            <tr>
                <td class="label">Bailleur</td>
                <td class="value">{{ $contrat->reservation->propriete->bailleur->personne->nomComplet() }}</td>
            </tr>
            <tr>
                <td class="label">Client (locataire/acquéreur)</td>
                <td class="value">{{ $contrat->reservation->client->personne->nomComplet() }}</td>
            </tr>
            <tr>
                <td class="label">E-mail client</td>
                <td class="value">{{ $contrat->reservation->client->personne->email }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Bien concerné</div>
        <table>
            <tr>
                <td class="label">Propriété</td>
                <td class="value">{{ $contrat->reservation->propriete->nom }}</td>
            </tr>
            <tr>
                <td class="label">Adresse</td>
                <td class="value">
                    {{ $contrat->reservation->propriete->rue }},
                    {{ $contrat->reservation->propriete->quartier }},
                    {{ $contrat->reservation->propriete->ville }},
                    {{ $contrat->reservation->propriete->pays }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Conditions du contrat</div>
        <table>
            <tr>
                <td class="label">Date de début</td>
                <td class="value">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</td>
            </tr>
            @if ($contrat->date_fin)
                <tr>
                    <td class="label">Date de fin</td>
                    <td class="value">{{ \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') }}</td>
                </tr>
            @endif
            @if ($contrat->mode_paiement_vente)
                <tr>
                    <td class="label">Mode de paiement</td>
                    <td class="value">
                        {{ $contrat->mode_paiement_vente === 'unique' ? 'Paiement unique' : 'Paiement échelonné' }}
                    </td>
                </tr>
            @endif
        </table>

        <div class="montant-box">
            <table>
                <tr>
                    <td class="label">Montant total du contrat</td>
                    <td class="value total">{{ number_format($contrat->montant_total, 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="signatures">
        <div class="signature-box">Le Bailleur</div>
        <div class="signature-box">Le Client</div>
    </div>

    <div class="footer">
        Document généré électroniquement par Location Immobilière — {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>

</html>
