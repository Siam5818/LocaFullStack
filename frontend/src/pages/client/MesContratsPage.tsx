/* cspell:disable */

import { useQuery } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { StatusBadge } from '@/shared/ui/StatusBadge';
import { EmptyState } from '@/shared/ui/EmptyState';
import { formatFCFA, formatDate } from '@/shared/lib/format';
import { getMesContrats, getTelechargerContratUrl } from '@/entities/contrat/api';
import apiClient from '@/shared/api/client';
import type { Paiement } from '@/entities/contrat/types';
import { useState } from 'react';

function downloadContrat(contratId: number) {
  const token = localStorage.getItem('auth_token');
  const url = getTelechargerContratUrl(contratId);
  fetch(url, { headers: { Authorization: `Bearer ${token}` } })
    .then((r) => r.blob())
    .then((blob) => {
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.setAttribute('download', `contrat-${contratId}.pdf`);
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    });
}

export default function MesContratsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['mes-contrats'],
    queryFn: getMesContrats,
  });

  const [paiements, setPaiements] = useState<Record<number, Paiement[]>>({});
  const [loadingPaiements, setLoadingPaiements] = useState<number | null>(null);

  async function togglePaiements(contratId: number) {
    if (paiements[contratId]) {
      setPaiements((p) => { const n = { ...p }; delete n[contratId]; return n; });
      return;
    }
    setLoadingPaiements(contratId);
    try {
      const { data } = await apiClient.get<Paiement[]>(`/mes-contrats/${contratId}/paiements`);
      setPaiements((p) => ({ ...p, [contratId]: data }));
    } finally {
      setLoadingPaiements(null);
    }
  }

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Mes contrats</h1>
        <div className="space-y-4">
          {isLoading && Array.from({ length: 2 }).map((_, i) => (
            <Skeleton key={i} className="h-32 w-full" />
          ))}
          {data?.map((c) => (
            <Card key={c.id}>
              <CardContent className="p-5 space-y-3">
                <div className="flex flex-wrap items-start justify-between gap-2">
                  <div>
                    <p className="font-semibold">Contrat #{c.id}</p>
                    <p className="text-sm text-gray-500">
                      {c.type_contrat.nom} · Du {formatDate(c.date_debut)}
                      {c.date_fin && ` au ${formatDate(c.date_fin)}`}
                    </p>
                    <p className="font-bold text-sm mt-1">{formatFCFA(c.montant_total)}</p>
                  </div>
                  <StatusBadge type="contrat" statut={c.statut} />
                </div>
                <div className="flex gap-2 flex-wrap">
                  <Button size="sm" variant="outline" onClick={() => downloadContrat(c.id)}>
                    📄 Télécharger le contrat
                  </Button>
                  <Button
                    size="sm"
                    variant="ghost"
                    onClick={() => togglePaiements(c.id)}
                    disabled={loadingPaiements === c.id}
                  >
                    {loadingPaiements === c.id
                      ? 'Chargement...'
                      : paiements[c.id]
                      ? 'Masquer les paiements'
                      : 'Voir les paiements'}
                  </Button>
                </div>
                {paiements[c.id] && (
                  <div className="border-t pt-3 space-y-2">
                    {paiements[c.id].map((p) => (
                      <div key={p.id} className="flex items-center justify-between text-sm">
                        <div>
                          <span className="font-medium">{formatFCFA(p.montant)}</span>
                          <span className="text-gray-400 ml-2">
                            Échéance : {formatDate(p.date_echeance)}
                          </span>
                        </div>
                        <div className="flex items-center gap-2">
                          <StatusBadge type="paiement" statut={p.statut} />
                          {p.numero_recu && (
                            <span className="text-xs font-mono text-gray-400">
                              {p.numero_recu}
                            </span>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                )}
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