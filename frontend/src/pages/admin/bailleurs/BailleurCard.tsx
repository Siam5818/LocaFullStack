/* cspell:disable */

import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import apiClient from '@/shared/api/client';

export interface BailleurAdminData {
  id: number;
  iban: string | null;
  nom_banque: string | null;
  personne: { nom: string; prenom: string; email: string; telephone: string; is_active: boolean };
}

export function BailleurCard({ bailleur: b }: Readonly<{ bailleur: BailleurAdminData }>) {
  const queryClient = useQueryClient();

  const { mutate: desactiver } = useMutation({
    mutationFn: () => apiClient.patch(`/admin/bailleurs/${b.id}/desactiver`),
    onSuccess: () => {
      toast.success('Bailleur désactivé.');
      queryClient.invalidateQueries({ queryKey: ['admin-bailleurs'] });
    },
  });

  return (
    <Card>
      <CardContent className="p-4 flex items-center justify-between">
        <div>
          <p className="font-semibold">{b.personne.prenom} {b.personne.nom}</p>
          <p className="text-sm text-gray-500">{b.personne.email} · {b.personne.telephone}</p>
          {b.nom_banque && <p className="text-xs text-gray-400">{b.nom_banque} · {b.iban}</p>}
        </div>
        <div className="flex items-center gap-3">
          <Badge variant={b.personne.is_active ? 'default' : 'destructive'}>
            {b.personne.is_active ? 'Actif' : 'Désactivé'}
          </Badge>
          {b.personne.is_active && (
            <Button size="sm" variant="outline" onClick={() => desactiver()}>Désactiver</Button>
          )}
        </div>
      </CardContent>
    </Card>
  );
}