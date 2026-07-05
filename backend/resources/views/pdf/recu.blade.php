<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $paiement->numero_recu }}</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #2c3e50;
            margin: 0;
            padding: 50px;
            background-color: #ffffff;
        }

        /* En-tête Moderne Asymétrique */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            border-bottom: 2px solid #edf2f7;
            padding-bottom: 20px;
        }

        .header-left h1 {
            font-size: 24px;
            color: #0f3d3e;
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .header-left .subtitle {
            font-size: 11px;
            color: #718096;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-right {
            text-align: right;
            vertical-align: bottom;
        }

        .numero {
            display: inline-block;
            background: #e6fffa;
            color: #0f3d3e;
            border: 1px solid #b2f5ea;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        /* Section Contenu Principal */
        table.content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.content-table th {
            text-align: left;
            padding: 10px 0;
            color: #a0aec0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #edf2f7;
        }

        table.content-table td {
            padding: 14px 0;
            border-bottom: 1px solid #f7fafc;
            vertical-align: middle;
        }

        .label {
            color: #4a5568;
            width: 40%;
            font-weight: 500;
        }

        .value {
            color: #1a202c;
            font-weight: bold;
            text-align: right;
        }

        /* Statuts et Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Bloc Montant Premium */
        .montant-card {
            background: #f8fafc;
            border-left: 5px solid #0f3d3e;
            border-radius: 4px;
            padding: 22px;
            margin-top: 40px;
        }

        .montant-table {
            width: 100%;
            border-collapse: collapse;
        }

        .montant-card .type {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .montant-card .montant {
            font-size: 26px;
            color: #0f3d3e;
            font-weight: bold;
            text-align: right;
        }

        /* Pied de page */
        .footer {
            position: absolute;
            bottom: 40px;
            left: 50px;
            right: 50px;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
            padding-top: 15px;
            letter-spacing: 0.2px;
        }
    </style>
</head>

<body>
    <!-- En-tête sous forme de tableau pour forcer l'alignement horizontal -->
    <table class="header-table">
        <tr>
            <td class="header-left">
                <h1>REÇU DE PAIEMENT</h1>
                <div class="subtitle">Immo - Location Immobilière</div>
            </td>
            <td class="header-right">
                <div class="numero">{{ $paiement->numero_recu }}</div>
            </td>
        </tr>
    </table>

    <!-- Tableau des détails -->
    <table class="content-table">
        <thead>
            <tr>
                <th>Désignation</th>
                <th style="text-align: right;">Informations correspondantes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Date de paiement</td>
                <td class="value">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Statut du paiement</td>
                <td class="value">
                    @if ($paiement->statut)
                        <span class="badge"
                            style="background-color: {{ $paiement->statut->couleur()['bg'] }}; color: {{ $paiement->statut->couleur()['texte'] }};">
                            {{ $paiement->statut->label() }}
                        </span>
                    @else
                        <span class="badge" style="background-color: #edf2f7; color: #4a5568;">Inconnu</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Contrat associé</td>
                <td class="value">N° {{ str_pad($paiement->contrat->id, 6, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td class="label">Propriété concernée</td>
                <td class="value" style="color: #0f3d3e;">{{ $paiement->contrat->reservation->propriete->nom }}</td>
            </tr>
            <tr>
                <td class="label">{{ $pourClient ? 'Payé par' : 'Versé à' }}</td>
                <td class="value">
                    @if ($pourClient)
                        {{ $paiement->contrat->reservation->client->personne->nomComplet() }}
                    @else
                        {{ $paiement->contrat->reservation->propriete->bailleur->personne->nomComplet() }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Méthode de paiement</td>
                <td class="value">
                    {{ $paiement->methode?->label() ?? 'Non spécifié' }}
                    @if ($paiement->methode === \App\Enums\MethodePaiement::MobileMoney && $paiement->operateur)
                        <span style="font-weight: normal; color: #718096;">({{ $paiement->operateur }})</span>
                    @endif
                </td>
            </tr>
            @if ($paiement->reference_transaction)
                <tr>
                    <td class="label">Référence transaction</td>
                    <td class="value" style="font-family: monospace; font-size: 12px; color: #4a5568;">
                        {{ $paiement->reference_transaction }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Boîte de montant style minimaliste haut de gamme -->
    <div class="montant-card">
        <table class="montant-table">
            <tr>
                <td class="type">
                    {{ $pourClient ? 'Montant total payé' : 'Montant net reçu (après commission)' }}
                </td>
                <td class="montant">
                    {{ number_format($montantAffiche, 0, ',', ' ') }} FCFA
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Reçu certifié et généré électroniquement par Immo - Location — {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>

</html>
