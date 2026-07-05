/* cspell:disable */

import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Link } from 'react-router-dom';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { getMesFavoris, supprimerFavori } from '@/entities/favorie/api';
import { formatFCFA } from '@/shared/lib/format';
import { toast } from 'sonner';

export default function MesFavorisPage() {
  const queryClient = useQueryClient();
  const { data: favoris, isLoading } = useQuery({
    queryKey: ['mes-favoris'],
    queryFn: getMesFavoris,
  });

  const { mutate: supprimer } = useMutation({
    mutationFn: supprimerFavori,
    onSuccess: () => {
      toast.success('Retiré des favoris.');
      queryClient.invalidateQueries({ queryKey: ['mes-favoris'] });
    },
    onError: () => toast.error('Erreur lors de la suppression.'),
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-3xl mx-auto px-4 py-8 w-full">
        <h1 className="text-2xl font-bold mb-6">Mes favoris</h1>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {isLoading && Array.from({ length: 4 }).map((_, i) => <Skeleton key={i} className="h-32 w-full" />)}
          {favoris?.map((f) => (
            <Card key={f.id}>
              <CardContent className="p-4 space-y-2">
                <Link to={`/proprietes/${f.propriete_id}`} className="font-semibold hover:underline block">
                  {f.propriete.nom}
                </Link>
                <p className="text-sm font-bold">{formatFCFA(f.propriete.cout)}</p>
                <Button variant="outline" size="sm" onClick={() => supprimer(f.id)}>
                  Retirer des favoris
                </Button>
              </CardContent>
            </Card>
          ))}
          {favoris?.length === 0 && !isLoading && (
            <p className="text-gray-500 text-sm text-center py-12 col-span-2">Aucun favori.</p>
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
}