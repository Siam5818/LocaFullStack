/* cspell:disable */

import apiClient from '@/shared/api/client';
import type { Contrat, Paiement } from './types';

export async function getMesContrats() {
  const { data } = await apiClient.get<Contrat[]>('/mes-contrats');
  return data;
}

export async function getMesContratPaiements(contratId: number) {
  const { data } = await apiClient.get<Paiement[]>(`/mes-contrats/${contratId}/paiements`);
  return data;
}

export function getTelechargerContratUrl(contratId: number) {
  return `${import.meta.env.VITE_API_URL}/mes-contrats/${contratId}/telecharger`;
}