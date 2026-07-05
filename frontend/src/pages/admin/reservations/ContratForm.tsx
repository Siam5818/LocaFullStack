/* cspell:disable */
import { useState } from 'react';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'sonner';
import apiClient from '@/shared/api/client';
import type { TypeContrat } from '@/entities/contrat/types';

interface Props {
  reservationId: number;
  onCancel: () => void;
}

async function getTypeContrats() {
  const { data } = await apiClient.get<TypeContrat[]>('/typecontrats');
  return data;
}

const empty = { typecontrat_id: '', date_debut: '', date_fin: '', montant_total: '' };

export function ContratForm({ reservationId, onCancel }: Readonly<Props>) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState(empty);
  const { data: typeContrats } = useQuery({ queryKey: ['typecontrats'], queryFn: getTypeContrats });

  const set = (key: keyof typeof form) =>
    (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) =>
      setForm((f) => ({ ...f, [key]: e.target.value }));

  const { mutate, isPending } = useMutation({
    mutationFn: () => apiClient.post(`/admin/reservations/${reservationId}/contrat`, {
      typecontrat_id: Number(form.typecontrat_id),
      date_debut: form.date_debut,
      date_fin: form.date_fin || undefined,
      montant_total: Number(form.montant_total),
    }),
    onSuccess: () => {
      toast.success('Contrat créé et envoyé par e-mail au client.');
      queryClient.invalidateQueries({ queryKey: ['admin-reservations'] });
      onCancel();
    },
    onError: (err: any) =>
      toast.error(err?.response?.data?.message ?? 'Erreur lors de la création du contrat.'),
  });

  return (
    <form
      onSubmit={(e) => { e.preventDefault(); mutate(); }}
      className="border rounded-lg p-4 space-y-3 bg-gray-50 mt-3"
    >
      <p className="font-medium text-sm">Créer le contrat</p>
      <div className="grid grid-cols-2 gap-3">
        <div>
          <Label className="text-xs">Type de contrat</Label>
          <select
            className="border rounded-md px-2 py-1.5 text-sm w-full"
            required
            value={form.typecontrat_id}
            onChange={set('typecontrat_id')}
          >
            <option value="">Choisir...</option>
            {typeContrats?.map((t) => <option key={t.id} value={t.id}>{t.nom}</option>)}
          </select>
        </div>
        <div>
          <Label className="text-xs">Montant total (FCFA)</Label>
          <Input type="number" required value={form.montant_total} onChange={set('montant_total')} />
        </div>
        <div>
          <Label className="text-xs">Date de début</Label>
          <Input type="date" required value={form.date_debut} onChange={set('date_debut')} />
        </div>
        <div>
          <Label className="text-xs">Date de fin (optionnelle)</Label>
          <Input type="date" value={form.date_fin} onChange={set('date_fin')} />
        </div>
      </div>
      <div className="flex gap-2">
        <Button type="submit" size="sm" disabled={isPending}>
          {isPending ? 'Création...' : 'Créer le contrat'}
        </Button>
        <Button type="button" size="sm" variant="outline" onClick={onCancel}>Annuler</Button>
      </div>
    </form>
  );
}