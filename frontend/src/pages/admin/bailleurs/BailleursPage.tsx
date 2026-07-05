/* cspell:disable */

import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import apiClient from '@/shared/api/client';
import { BailleurCard, type BailleurAdminData } from './BailleurCard';
import { BailleurForm } from './BailleurForm';

async function getBailleurs() {
  const { data } = await apiClient.get<BailleurAdminData[]>('/admin/bailleurs');
  return data;
}

export default function AdminBailleursPage() {
  const [showForm, setShowForm] = useState(false);
  const { data, isLoading } = useQuery({ queryKey: ['admin-bailleurs'], queryFn: getBailleurs });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full space-y-5">
        <div className="flex items-center justify-between">
          <h1 className="text-2xl font-bold">Bailleurs ({data?.length ?? 0})</h1>
          <Button onClick={() => setShowForm((v) => !v)} variant={showForm ? 'outline' : 'default'}>
            {showForm ? 'Annuler' : '+ Nouveau bailleur'}
          </Button>
        </div>
        {showForm && <BailleurForm onClose={() => setShowForm(false)} />}
        <div className="space-y-3">
          {isLoading && Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-20 w-full" />)}
          {data?.map((b) => <BailleurCard key={b.id} bailleur={b} />)}
          {data?.length === 0 && !isLoading && (
            <p className="text-gray-500 text-sm text-center py-12">Aucun bailleur.</p>
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
}