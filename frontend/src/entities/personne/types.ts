/* cspell:disable */

export type Role = 'admin' | 'client' | 'bailleur';

export interface Personne {
  id: number;
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  role: Role;
  is_active: boolean;
  email_verified_at: string | null;
  client?: { id: number; date_naissance: string | null; adresse: string | null };
  admin?: { id: number; niveau_acces: string };
  bailleur?: { id: number; iban: string | null; nom_banque: string | null };
}

export interface LoginPayload { email: string; password: string }
export interface RegisterPayload {
  nom: string; prenom: string; email: string; telephone: string;
  password: string; password_confirmation: string;
}
export interface LoginResponse {
  message: string;
  token: string;
  user: { id: number; nom: string; prenom: string; email: string; role: Role };
}