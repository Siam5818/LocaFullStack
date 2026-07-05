/* cspell:disable */

import { useQuery } from '@tanstack/react-query';
import { Link } from 'react-router-dom';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { formatFCFA } from '@/shared/lib/format';
import apiClient from '@/shared/api/client';

interface Stats {
  reservations_en_attente: number;
  contrats_actifs: number;
  proprietes_disponibles: number;
  nouveaux_clients_mois: number;
  paiements_en_retard: number;
  revenus_commission_mois: number;
}

async function getStats() {
  const { data } = await apiClient.get<Stats>('/admin/dashboard/stats');
  return data;
}

export default function AdminDashboardPage() {
  const { data, isLoading } = useQuery({ queryKey: ['admin-stats'], queryFn: getStats });

  const stats = data ? [
    { label: 'Réservations en attente', value: data.reservations_en_attente, isMoney: false, urgent: data.reservations_en_attente > 0 },
    { label: 'Contrats actifs', value: data.contrats_actifs, isMoney: false, urgent: false },
    { label: 'Propriétés disponibles', value: data.proprietes_disponibles, isMoney: false, urgent: false },
    { label: 'Nouveaux clients (mois)', value: data.nouveaux_clients_mois, isMoney: false, urgent: false },
    { label: 'Paiements en retard', value: data.paiements_en_retard, isMoney: false, urgent: data.paiements_en_retard > 0 },
    { label: 'Commission du mois', value: data.revenus_commission_mois, isMoney: true, urgent: false },
  ] : [];

  const links = [
    { label: 'Gérer les réservations', to: '/admin/reservations' },
    { label: 'Gérer les propriétés', to: '/admin/proprietes' },
    { label: 'Gérer les clients', to: '/admin/clients' },
    { label: 'Gérer les bailleurs', to: '/admin/bailleurs' },
  ];

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-5xl mx-auto px-4 py-8 w-full space-y-8">
        <h1 className="text-2xl font-bold">Tableau de bord Admin</h1>

        <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
          {isLoading && Array.from({ length: 6 }).map((_, i) => <Skeleton key={i} className="h-24 w-full" />)}
          {stats.map((s) => (
            <Card key={s.label} className={s.urgent ? 'border-orange-400' : ''}>
              <CardContent className="p-4">
                <p className="text-xs text-gray-500 mb-1">{s.label}</p>
                <p className={`font-bold text-xl ${s.urgent ? 'text-orange-500' : ''}`}>
                  {s.isMoney ? formatFCFA(s.value) : s.value}
                </p>
              </CardContent>
            </Card>
          ))}
        </div>

        <div>
          <h2 className="font-semibold mb-3">Actions rapides</h2>
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
            {links.map((l) => (
              <Link key={l.to} to={l.to}>
                <Card className="hover:shadow-md transition-shadow cursor-pointer">
                  <CardContent className="p-4 text-sm font-medium text-center">{l.label}</CardContent>
                </Card>
              </Link>
            ))}
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
}