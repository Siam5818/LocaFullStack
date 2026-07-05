/* cspell:disable */

import { useState, ChangeEvent } from 'react';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { ProprieteCard } from '@/widgets/propriete-card/ProprieteCard';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { useProprietesInfinite } from '@/features/proprietes-filtrage/useProprietesFilters';
import { useTypeProprietes, useServices } from '@/entities/propriete/hooks';
import type { ProprieteFilters } from '@/entities/propriete/types';

export default function ProprietesPage() {
  const [filters, setFilters] = useState<Omit<ProprieteFilters, 'cursor'>>({ tri: 'recent' });
  const { data, isLoading, fetchNextPage, hasNextPage, isFetchingNextPage } = useProprietesInfinite(filters);
  const { data: types } = useTypeProprietes();
  const { data: services } = useServices();

  const proprietes = data?.pages.flatMap((p) => p.data) ?? [];

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-6xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Propriétés disponibles</h1>

        <div className="flex flex-wrap gap-3 mb-8">
          <Input
            placeholder="Ville"
            className="w-40"
            onChange={(e: ChangeEvent<HTMLInputElement>) => setFilters((f) => ({ ...f, ville: e.target.value || undefined }))}
          />
          <select
            className="border rounded-md px-3 text-sm"
            onChange={(e) => setFilters((f) => ({ ...f, type_id: e.target.value ? Number(e.target.value) : undefined }))}
          >
            <option value="">Tous types</option>
            {types?.map((t) => <option key={t.id} value={t.id}>{t.nom}</option>)}
          </select>
          <select
            className="border rounded-md px-3 text-sm"
            onChange={(e) => setFilters((f) => ({ ...f, service_id: e.target.value ? Number(e.target.value) : undefined }))}
          >
            <option value="">Location ou vente</option>
            {services?.map((s) => <option key={s.id} value={s.id}>{s.nom}</option>)}
          </select>
          <select
            className="border rounded-md px-3 text-sm"
            onChange={(e) => setFilters((f) => ({ ...f, tri: e.target.value as ProprieteFilters['tri'] }))}
          >
            <option value="recent">Plus récent</option>
            <option value="prix_asc">Prix croissant</option>
            <option value="prix_desc">Prix décroissant</option>
          </select>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {isLoading &&
            Array.from({ length: 6 }).map((_, i) => <Skeleton key={`skeleton-${i}`} className="h-72 rounded-lg" />)}
          {proprietes.map((propriete) => (
            <ProprieteCard key={propriete.id} propriete={propriete} />))
          }
        </div>

        {proprietes.length === 0 && !isLoading && (
          <p className="text-center text-gray-500 py-12">Aucune propriété ne correspond à votre recherche.</p>
        )}

        {hasNextPage && (
          <div className="text-center mt-8">
            <Button variant="outline" onClick={() => fetchNextPage()} disabled={isFetchingNextPage}>
              {isFetchingNextPage ? 'Chargement...' : 'Voir plus'}
            </Button>
          </div>
        )}
      </main>
      <Footer />
    </div>
  );
}