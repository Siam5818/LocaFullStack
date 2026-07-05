/* cspell:disable */

import { useQuery } from '@tanstack/react-query';
import { getProprieteById, getTypeProprietes, getServices, getEquipements } from './api';

export function useProprieteDetail(id: number) {
  return useQuery({
    queryKey: ['propriete', id],
    queryFn: () => getProprieteById(id),
  });
}

export function useTypeProprietes() {
  return useQuery({ queryKey: ['typeproprietes'], queryFn: getTypeProprietes });
}

export function useServices() {
  return useQuery({ queryKey: ['services'], queryFn: getServices });
}

export function useEquipements() {
  return useQuery({ queryKey: ['equipements'], queryFn: getEquipements });
}