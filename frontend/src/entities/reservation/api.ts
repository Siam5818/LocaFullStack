/* cspell:disable */

import apiClient from '@/shared/api/client';
import type { Reservation } from './types';

export async function getMesReservations() {
  const { data } = await apiClient.get<Reservation[]>('/mes-reservations');
  return data;
}

export async function creerReservation(propriete_id: number) {
  const { data } = await apiClient.post<{ message: string; reservation: Reservation }>(
    '/mes-reservations',
    { propriete_id }
  );
  return data;
}

export async function annulerReservation(id: number) {
  const { data } = await apiClient.delete<{ message: string }>(`/mes-reservations/${id}`);
  return data;
}