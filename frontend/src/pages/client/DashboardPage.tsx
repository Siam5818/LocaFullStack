/* cspell:disable */

import { useQuery } from '@tanstack/react-query';
import { Link } from 'react-router-dom';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { ReservationList } from '@/widgets/reservation-list/ReservationList';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { useAuth } from '@/features/auth/useAuth';
import { getMesReservations } from '@/entities/reservation/api';
import { getMesFavoris } from '@/entities/favorie/api';
import { formatFCFA } from '@/shared/lib/format';

export default function DashboardPage() {
  const { user } = useAuth();

  const { data: reservations, isLoading: rLoad } = useQuery({
    queryKey: ['mes-reservations'],
    queryFn: getMesReservations,
  });

  const { data: favoris, isLoading: fLoad } = useQuery({
    queryKey: ['mes-favoris'],
    queryFn: getMesFavoris,
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-4xl mx-auto px-4 py-8 w-full space-y-8">
        <div>
          <h1 className="text-2xl font-bold">Bonjour, {user?.prenom} 👋</h1>
          <p className="text-gray-500 text-sm">Bienvenue dans votre espace client.</p>
        </div>

        <section>
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Mes réservations récentes</h2>
            <Link to="/espace-client/reservations">
              <Button variant="outline" size="sm">Tout voir</Button>
            </Link>
          </div>
          {rLoad
            ? <Skeleton className="h-20 w-full" />
            : <ReservationList reservations={reservations ?? []} limit={3} />
          }
        </section>

        <section>
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Mes favoris récents</h2>
            <Link to="/espace-client/favoris">
              <Button variant="outline" size="sm">Tout voir</Button>
            </Link>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {fLoad && <Skeleton className="h-24 w-full col-span-2" />}
            {favoris?.slice(0, 4).map((f) => (
              <Link to={`/proprietes/${f.propriete_id}`} key={f.id}>
                <Card className="hover:shadow-sm transition-shadow">
                  <CardContent className="p-4">
                    <p className="font-medium">{f.propriete.nom}</p>
                    <p className="text-sm font-bold text-gray-700">{formatFCFA(f.propriete.cout)}</p>
                  </CardContent>
                </Card>
              </Link>
            ))}
            {favoris?.length === 0 && !fLoad && (
              <p className="text-sm text-gray-400 col-span-2">Aucun favori pour l'instant.</p>
            )}
          </div>
        </section>
      </main>
      <Footer />
    </div>
  );
}