<x-mail::message>
# Votre contrat est signé !

Bonjour {{ $contrat->reservation->client->personne->prenom }},

Nous avons le plaisir de vous confirmer que votre contrat pour la propriété
**{{ $contrat->reservation->propriete->nom }}** a bien été signé et enregistré.

<x-mail::panel>
**Numéro de contrat :** {{ str_pad($contrat->id, 6, '0', STR_PAD_LEFT) }}<br>
**Date de début :** {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}<br>
**Montant total :** {{ number_format($contrat->montant_total, 0, ',', ' ') }} FCFA
</x-mail::panel>

Vous trouverez le contrat complet en pièce jointe de cet e-mail (format PDF).
Vous pourrez également le retélécharger à tout moment depuis votre espace client.

Merci de votre confiance.

Cordialement,<br>
L'équipe Location Immobilière
</x-mail::message>
