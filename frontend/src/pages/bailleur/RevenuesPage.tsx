/* cspell:disable */
import { useQuery } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { formatFCFA } from '@/shared/lib/format';
import apiClient from '@/shared/api/client';

interface ResumeRevenu {
  total_brut_contrats: number;
  total_commission: number;
  total_paye_par_clients: number;
  net_percu_estime: number;
  en_attente: number;
  en_retard: number;
  nombre_contrats_actifs: number;
}

async function getMesRevenus() {
  const { data } = await apiClient.get<ResumeRevenu>('/bailleur/revenus');
  return data;
}

export default function RevenuesPage() {
  const { data, isLoading } = useQuery({ queryKey: ['bailleur-revenus'], queryFn: getMesRevenus });

  const stats = data ? [
    { label: 'Contrats actifs', value: data.nombre_contrats_actifs, isMoney: false },
    { label: 'Total brut des contrats', value: data.total_brut_contrats, isMoney: true },
    { label: 'Commission plateforme', value: data.total_commission, isMoney: true },
    { label: 'Net perçu estimé', value: data.net_percu_estime, isMoney: true },
    { label: 'En attente', value: data.en_attente, isMoney: true },
    { label: 'En retard', value: data.en_retard, isMoney: true },
  ] : [];

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Mes revenus</h1>
        <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
          {isLoading && Array.from({ length: 6 }).map((_, i) => <Skeleton key={i} className="h-24 w-full" />)}
          {stats.map((s) => (
            <Card key={s.label}>
              <CardContent className="p-4">
                <p className="text-xs text-gray-500 mb-1">{s.label}</p>
                <p className="font-bold text-lg">
                  {s.isMoney ? formatFCFA(s.value) : s.value}
                </p>
              </CardContent>
            </Card>
          ))}
        </div>
      </main>
      <Footer />
    </div>
  );
}