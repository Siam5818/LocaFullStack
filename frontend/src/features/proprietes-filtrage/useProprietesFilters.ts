import { useInfiniteQuery } from '@tanstack/react-query';
import { getProprietes } from '@/entities/propriete/api';
import type { ProprieteFilters } from '@/entities/propriete/types';

export function useProprietesInfinite(filters: Omit<ProprieteFilters, 'cursor'>) {
  return useInfiniteQuery({
    queryKey: ['proprietes', filters],
    queryFn: ({ pageParam }) => getProprietes({ ...filters, cursor: pageParam }),
    initialPageParam: undefined as string | undefined,
    getNextPageParam: (lastPage) => lastPage.next_cursor ?? undefined,
  });
}