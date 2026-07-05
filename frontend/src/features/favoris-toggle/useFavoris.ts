/* cspell:disable */

import { useMutation, useQueryClient } from '@tanstack/react-query';
import { toast } from 'sonner';
import { ajouterFavori, supprimerFavori } from '@/entities/favorie/api';

export function useAjouterFavori() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (proprieteId: number) => ajouterFavori(proprieteId),
    onSuccess: () => {
      toast.success('Ajouté aux favoris.');
      queryClient.invalidateQueries({ queryKey: ['mes-favoris'] });
    },
    onError: () => toast.error('Erreur lors de l\'ajout aux favoris.'),
  });
}

export function useSupprimerFavori() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (favorieId: number) => supprimerFavori(favorieId),
    onSuccess: () => {
      toast.success('Retiré des favoris.');
      queryClient.invalidateQueries({ queryKey: ['mes-favoris'] });
    },
    onError: () => toast.error('Erreur lors de la suppression.'),
  });
}