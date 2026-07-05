/* cspell:disable */

import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { ReservationList } from '@/widgets/reservation-list/ReservationList';
import { LoadingSpinner } from '@/shared/ui/LoadingSpinner';
import { getMesReservations, annulerReservation } from '@/entities/reservation/api';
import { toast } from 'sonner';

export default function MesReservationsPage() {
  const queryClient = useQueryClient();

  const { data: reservations, isLoading } = useQuery({
    queryKey: ['mes-reservations'],
    queryFn: getMesReservations,
  });

  const { mutate: annuler, isPending } = useMutation({
    mutationFn: annulerReservation,
    onSuccess: () => {
      toast.success('Réservation annulée.');
      queryClient.invalidateQueries({ queryKey: ['mes-reservations'] });
    },
    onError: () => toast.error('Impossible d\'annuler cette réservation.'),
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-3xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Mes réservations</h1>
        {isLoading
          ? <LoadingSpinner />
          : <ReservationList
              reservations={reservations ?? []}
              onAnnuler={annuler}
              isPending={isPending}
            />
        }
      </main>
      <Footer />
    </div>
  );
}