/* cspell:disable */

import apiClient from '@/shared/api/client';
import type { Favorie } from './types';

export async function getMesFavoris() {
  const { data } = await apiClient.get<Favorie[]>('/mes-favoris');
  return data;
}

export async function ajouterFavori(propriete_id: number) {
  const { data } = await apiClient.post<{ message: string; favorie: Favorie }>('/favoris', { propriete_id });
  return data;
}

export async function supprimerFavori(id: number) {
  const { data } = await apiClient.delete<{ message: string }>(`/favoris/${id}`);
  return data;
}