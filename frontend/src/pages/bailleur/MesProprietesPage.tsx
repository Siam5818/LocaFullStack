/* cspell:disable */

import { useQuery } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { Badge } from '@/components/ui/badge';
import { formatFCFA } from '@/shared/lib/format';
import apiClient from '@/shared/api/client';
import type { Propriete } from '@/entities/propriete/types';

async function getMesProprietes() {
  const { data } = await apiClient.get<Propriete[]>('/bailleur/proprietes');
  return data;
}

export default function MesProprietesPage() {
  const { data, isLoading } = useQuery({ queryKey: ['bailleur-proprietes'], queryFn: getMesProprietes });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Mes propriétés</h1>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {isLoading && Array.from({ length: 4 }).map((_, i) => <Skeleton key={i} className="h-32 w-full" />)}
          {data?.map((p) => (
            <Card key={p.id}>
              <CardContent className="p-4 space-y-2">
                <div className="flex justify-between items-start">
                  <p className="font-semibold">{p.nom}</p>
                  <Badge variant="outline">{p.service.nom}</Badge>
                </div>
                <p className="text-sm text-gray-500">{p.quartier}, {p.ville}</p>
                <p className="font-bold">{formatFCFA(p.cout)}</p>
                <p className="text-xs text-gray-400">{p.nombre_piece} pièce(s) · {p.dimension} m²</p>
              </CardContent>
            </Card>
          ))}
          {data?.length === 0 && !isLoading && (
            <p className="text-gray-500 text-sm text-center py-12 col-span-2">Aucune propriété.</p>
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
}