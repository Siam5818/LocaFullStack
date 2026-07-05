/* cspell:disable */

export interface TypeContrat {
  id: number;
  nom: string;
  duree_mois: number | null;
  taux_commission_defaut: string;
}

export interface Contrat {
  id: number;
  reservation_id: number;
  date_debut: string;
  date_fin: string | null;
  montant_total: string;
  taux_commission_applique: string;
  montant_commission: string;
  mode_paiement_vente: 'unique' | 'echelonne' | null;
  statut: 'actif' | 'termine' | 'resilie';
  type_contrat: { id: number; nom: string };
}

export interface Paiement {
  id: number;
  contrat_id: number;
  montant: string;
  date_echeance: string;
  date_paiement: string | null;
  statut: 'en_attente' | 'paye' | 'en_retard';
  methode: 'mobile_money' | 'cash' | 'carte_bancaire' | null;
  operateur: string | null;
  numero_recu: string | null;
}