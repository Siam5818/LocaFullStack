/* cspell:disable */

import { useQuery } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Skeleton } from '@/components/ui/skeleton';
import apiClient from '@/shared/api/client';
import { ReservationCard, type ReservationAdminData } from './ReservationCard';

async function getReservations() {
  const { data } = await apiClient.get<ReservationAdminData[]>('/admin/reservations');
  return data;
}

export default function AdminReservationsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['admin-reservations'],
    queryFn: getReservations,
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-5xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Gestion des réservations</h1>
        <div className="space-y-4">
          {isLoading && Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-28 w-full" />)}
          {data?.map((r) => <ReservationCard key={r.id} reservation={r} />)}
          {data?.length === 0 && !isLoading && (
            <p className="text-gray-500 text-sm text-center py-12">Aucune réservation.</p>
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
}