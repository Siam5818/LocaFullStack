/* cspell:disable */

export const STATUT_RESERVATION_LABELS: Record<
  string,
  { label: string; variant: 'default' | 'secondary' | 'destructive' | 'outline' }
> = {
  en_attente: { label: 'En attente', variant: 'secondary' },
  confirmee:  { label: 'Confirmée', variant: 'default' },
  annulee:    { label: 'Annulée', variant: 'destructive' },
};

export const STATUT_CONTRAT_LABELS: Record<
  string,
  { label: string; variant: 'default' | 'secondary' | 'destructive' | 'outline' }
> = {
  actif:    { label: 'Actif', variant: 'default' },
  termine:  { label: 'Terminé', variant: 'secondary' },
  resilie:  { label: 'Résilié', variant: 'destructive' },
};

export const STATUT_PAIEMENT_LABELS: Record<
  string,
  { label: string; variant: 'default' | 'secondary' | 'destructive' | 'outline' }
> = {
  en_attente: { label: 'En attente', variant: 'secondary' },
  paye:       { label: 'Payé', variant: 'default' },
  en_retard:  { label: 'En retard', variant: 'destructive' },
};

export const METHODE_PAIEMENT_LABELS: Record<string, string> = {
  mobile_money:   'Mobile Money',
  cash:           'Espèces',
  carte_bancaire: 'Carte bancaire',
};

export const ROLE_LABELS: Record<string, string> = {
  admin:    'Administrateur',
  client:   'Client',
  bailleur: 'Bailleur',
};
