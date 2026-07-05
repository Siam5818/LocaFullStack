/* cspell:disable */

import { useQuery } from '@tanstack/react-query';
import { useParams } from 'react-router-dom';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { StatusBadge } from '@/shared/ui/StatusBadge';
import { EmptyState } from '@/shared/ui/EmptyState';
import { formatFCFA, formatDate } from '@/shared/lib/format';
import { METHODE_PAIEMENT_LABELS } from '@/shared/lib/constants';
import apiClient from '@/shared/api/client';
import type { Paiement } from '@/entities/contrat/types';

async function getPaiements(contratId: string) {
  const { data } = await apiClient.get<Paiement[]>(
    `/bailleur/contrats/${contratId}/paiements`
  );
  return data;
}

function downloadRecu(contratId: string, paiementId: number) {
  const token = localStorage.getItem('auth_token');
  const url = `${import.meta.env.VITE_API_URL}/bailleur/contrats/${contratId}/paiements/${paiementId}/recu`;
  fetch(url, { headers: { Authorization: `Bearer ${token}` } })
    .then((r) => r.blob())
    .then((blob) => {
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.setAttribute('download', `recu-bailleur-${paiementId}.pdf`);
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    });
}

export default function BailleurPaiementsPage() {
  const { contratId } = useParams<{ contratId: string }>();
  const { data, isLoading } = useQuery({
    queryKey: ['bailleur-paiements', contratId],
    queryFn: () => getPaiements(contratId!),
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">
          Paiements — Contrat #{contratId}
        </h1>
        <div className="space-y-3">
          {isLoading && Array.from({ length: 3 }).map((_, i) => (
            <Skeleton key={i} className="h-24 w-full" />
          ))}
          {data?.map((p) => (
            <Card key={p.id}>
              <CardContent className="p-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                  <p className="font-bold">{formatFCFA(p.montant)}</p>
                  <p className="text-sm text-gray-500">
                    Échéance : {formatDate(p.date_echeance)}
                    {p.date_paiement && ` · Payé le ${formatDate(p.date_paiement)}`}
                  </p>
                  {p.methode && (
                    <p className="text-xs text-gray-400">
                      {METHODE_PAIEMENT_LABELS[p.methode]}
                      {p.operateur && ` (${p.operateur})`}
                    </p>
                  )}
                  {p.numero_recu && (
                    <p className="text-xs font-mono text-gray-500">{p.numero_recu}</p>
                  )}
                </div>
                <div className="flex items-center gap-2">
                  <StatusBadge type="paiement" statut={p.statut} />
                  {p.numero_recu && (
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => downloadRecu(contratId!, p.id)}
                    >
                      🧾 Mon reçu
                    </Button>
                  )}
                </div>
              </CardContent>
            </Card>
          ))}
          {data?.length === 0 && !isLoading && (
            <EmptyState message="Aucun paiement enregistré." icon="💳" />
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
}