/* cspell:disable */

export interface Favorie {
  id: number;
  client_id: number;
  propriete_id: number;
  propriete: { id: number; nom: string; cout: string; image_principale?: { chemin: string }[] };
}