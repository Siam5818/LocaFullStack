/* cspell:disable */

import { useQuery } from '@tanstack/react-query';
import { Link } from 'react-router-dom';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { StatusBadge } from '@/shared/ui/StatusBadge';
import { EmptyState } from '@/shared/ui/EmptyState';
import { formatFCFA, formatDate } from '@/shared/lib/format';
import apiClient from '@/shared/api/client';
import type { Contrat } from '@/entities/contrat/types';

interface ContratBailleur extends Contrat {
  reservation: {
    propriete: { nom: string; ville: string };
    client: { personne: { nom: string; prenom: string } };
  };
}

async function getMesContrats() {
  const { data } = await apiClient.get<ContratBailleur[]>('/bailleur/contrats');
  return data;
}

export default function MesContratsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['bailleur-contrats'],
    queryFn: getMesContrats,
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Mes contrats</h1>
        <div className="space-y-4">
          {isLoading && Array.from({ length: 3 }).map((_, i) => (
            <Skeleton key={i} className="h-28 w-full" />
          ))}
          {data?.map((c) => (
            <Card key={c.id}>
              <CardContent className="p-5 space-y-3">
                <div className="flex flex-wrap items-start justify-between gap-2">
                  <div>
                    <p className="font-semibold">{c.reservation.propriete.nom}</p>
                    <p className="text-sm text-gray-500">
                      Client : {c.reservation.client.personne.prenom}{' '}
                      {c.reservation.client.personne.nom}
                    </p>
                    <p className="text-sm text-gray-500">
                      Du {formatDate(c.date_debut)}
                      {c.date_fin && ` au ${formatDate(c.date_fin)}`}
                    </p>
                  </div>
                  <div className="text-right space-y-1">
                    <StatusBadge type="contrat" statut={c.statut} />
                    <p className="font-bold text-sm">{formatFCFA(c.montant_total)}</p>
                  </div>
                </div>
                <Link to={`/bailleur/contrats/${c.id}/paiements`}>
                  <Button size="sm" variant="outline">Voir les paiements</Button>
                </Link>
              </CardContent>
            </Card>
          ))}
          {data?.length === 0 && !isLoading && (
            <EmptyState message="Aucun contrat pour le moment." icon="📋" />
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
}