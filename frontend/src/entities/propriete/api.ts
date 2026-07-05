/* cspell:disable */

import apiClient from '@/shared/api/client';
import type { CursorPaginatedResponse } from '@/shared/types/api';
import type { Propriete, ProprieteDetailResponse, ProprieteFilters, TypePropriete, Service, Equipement } from './types';

export async function getProprietes(filters: ProprieteFilters = {}) {
  const { data } = await apiClient.get<CursorPaginatedResponse<Propriete>>('/proprietes', { params: filters });
  return data;
}

export async function getProprieteById(id: number) {
  const { data } = await apiClient.get<ProprieteDetailResponse>(`/proprietes/${id}`);
  return data;
}

export async function getTypeProprietes() {
  const { data } = await apiClient.get<TypePropriete[]>('/typeproprietes');
  return data;
}

export async function getServices() {
  const { data } = await apiClient.get<Service[]>('/services');
  return data;
}

export async function getEquipements() {
  const { data } = await apiClient.get<Equipement[]>('/equipements');
  return data;
}