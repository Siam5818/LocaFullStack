/* cspell:disable */

export interface TypePropriete { id: number; nom: string; description: string | null }
export interface Service { id: number; nom: string }
export interface Equipement { id: number; nom: string; icone: string | null }
export interface ImagePropriete { id: number; chemin: string; is_principale: boolean; ordre: number }
export interface Bailleur {
  id: number;
  personne: { nom: string; prenom: string; email: string; telephone: string };
}

export interface Propriete {
  id: number;
  nom: string;
  rue: string | null;
  quartier: string | null;
  ville: string;
  pays: string;
  latitude: string | null;
  longitude: string | null;
  nombre_piece: number;
  dimension: number;
  description: string;
  cout: string;
  type_propriete: TypePropriete;
  service: Service;
  bailleur: Bailleur;
  images?: ImagePropriete[];
  image_principale?: ImagePropriete[];
  equipements: Equipement[];
  created_at: string;
}

export interface Note {
  id: number;
  note: number;
  commentaire: string | null;
  client: { personne: { nom: string; prenom: string } };
  created_at: string;
}

export interface ProprieteDetailResponse {
  propriete: Propriete & { notes: Note[] };
  note_moyenne: number;
  nombre_avis: number;
}

export interface ProprieteFilters {
  ville?: string;
  prix_min?: number;
  prix_max?: number;
  type_id?: number;
  service_id?: number;
  piece_min?: number;
  tri?: 'recent' | 'prix_asc' | 'prix_desc';
  cursor?: string;
}