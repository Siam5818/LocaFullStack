/* cspell:disable */

import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import apiClient from '@/shared/api/client';
import type { Propriete } from '@/entities/propriete/types';
import { ProprieteAdminCard } from './ProprieteAdminCard';
import { ProprieteForm } from './ProprieteForm';

async function getProprietes() {
  const { data } = await apiClient.get<{ data: Propriete[] }>('/proprietes');
  return data.data ?? [];
}

export default function AdminProprietesPage() {
  const [showForm, setShowForm] = useState(false);
  const { data, isLoading } = useQuery({ queryKey: ['admin-proprietes'], queryFn: getProprietes });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-5xl mx-auto px-4 py-8 w-full space-y-6">
        <div className="flex items-center justify-between">
          <h1 className="text-2xl font-bold">Propriétés ({data?.length ?? 0})</h1>
          <Button onClick={() => setShowForm((v) => !v)} variant={showForm ? 'outline' : 'default'}>
            {showForm ? 'Annuler' : '+ Nouvelle propriété'}
          </Button>
        </div>
        {showForm && <ProprieteForm onClose={() => setShowForm(false)} />}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {isLoading && Array.from({ length: 4 }).map((_, i) => <Skeleton key={i} className="h-32 w-full" />)}
          {data?.map((p) => <ProprieteAdminCard key={p.id} propriete={p} />)}
          {data?.length === 0 && !isLoading && (
            <p className="text-gray-500 text-sm text-center py-12 col-span-2">Aucune propriété.</p>
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
}