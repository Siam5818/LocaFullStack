/* cspell:disable */

import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';
import apiClient from '@/shared/api/client';

export interface ClientAdminData {
  id: number;
  personne: {
    nom: string;
    prenom: string;
    email: string;
    telephone: string;
    is_active: boolean;
  };
}

export function ClientCard({ client: c }: Readonly<{ client: ClientAdminData }>) {
  const queryClient = useQueryClient();

  const { mutate: desactiver } = useMutation({
    mutationFn: () => apiClient.patch(`/admin/clients/${c.id}/desactiver`),
    onSuccess: () => {
      toast.success('Client désactivé.');
      queryClient.invalidateQueries({ queryKey: ['admin-clients'] });
    },
  });

  const { mutate: activer } = useMutation({
    mutationFn: () => apiClient.patch(`/admin/clients/${c.id}/activer`),
    onSuccess: () => {
      toast.success('Client activé.');
      queryClient.invalidateQueries({ queryKey: ['admin-clients'] });
    },
  });

  return (
    <Card>
      <CardContent className="p-4 flex items-center justify-between">
        <div>
          <p className="font-semibold">{c.personne.prenom} {c.personne.nom}</p>
          <p className="text-sm text-gray-500">{c.personne.email} · {c.personne.telephone}</p>
        </div>
        <div className="flex items-center gap-3">
          <Badge variant={c.personne.is_active ? 'default' : 'destructive'}>
            {c.personne.is_active ? 'Actif' : 'Désactivé'}
          </Badge>
          {c.personne.is_active
            ? <Button size="sm" variant="outline" onClick={() => desactiver()}>Désactiver</Button>
            : <Button size="sm" onClick={() => activer()}>Activer</Button>
          }
        </div>
      </CardContent>
    </Card>
  );
}