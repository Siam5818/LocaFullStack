<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PdfService
{
    /**
     * Génère le PDF du contrat signé.
     * Retourne le contenu binaire du PDF (pas un fichier sur disque).
     */
    public function genererContrat(Contrat $contrat): string
    {
        $contrat->load([
            'reservation.client.personne',
            'reservation.propriete.bailleur.personne',
            'typeContrat',
        ]);

        $pdf = Pdf::loadView('pdf.contrat', [
            'contrat' => $contrat,
        ]);

        return $pdf->output();
    }

    /**
     * Génère le PDF du reçu de paiement.
     * $pourClient = true  -> montre le montant TOTAL payé par le client.
     * $pourClient = false -> montre le montant NET reçu par le bailleur (après commission).
     */
    public function genererRecu(Paiement $paiement, bool $pourClient = true): string
    {
        $paiement->load([
            'contrat.reservation.client.personne',
            'contrat.reservation.propriete.bailleur.personne',
        ]);

        $montantAffiche = $pourClient
            ? $paiement->montant
            : bcsub((string) $paiement->montant, $this->commissionProportionnelle($paiement), 2);

        $pdf = Pdf::loadView('pdf.recu', [
            'paiement'       => $paiement,
            'montantAffiche' => $montantAffiche,
            'pourClient'     => $pourClient,
        ]);

        return $pdf->output();
    }

    /**
     * Nom de fichier suggéré pour le téléchargement.
     */
    public function nomFichierContrat(Contrat $contrat): string
    {
        return Str::slug("contrat-{$contrat->id}-" . now()->format('Y-m-d')) . '.pdf';
    }

    public function nomFichierRecu(Paiement $paiement): string
    {
        $numero = $paiement->numero_recu ?? "paiement-{$paiement->id}";
        return Str::slug($numero) . '.pdf';
    }

    /**
     * Calcule la part de commission proportionnelle à ce paiement spécifique,
     * en répartissant la commission totale du contrat sur l'ensemble des paiements prévus.
     */
    private function commissionProportionnelle(Paiement $paiement): string
    {
        $contrat = $paiement->contrat;

        if ((float) $contrat->montant_total === 0.0) {
            return '0';
        }

        $proportion = bcdiv((string) $paiement->montant, (string) $contrat->montant_total, 6);

        return bcmul($proportion, (string) $contrat->montant_commission, 2);
    }
}
