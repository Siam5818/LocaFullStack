/* cspell:disable */

export type StatutReservation = 'en_attente' | 'confirmee' | 'annulee';

export interface Reservation {
  id: number;
  client_id: number;
  propriete_id: number;
  statut: StatutReservation;
  date_soumission: string;
  propriete?: { id: number; nom: string; image_principale?: { chemin: string }[] };
}
