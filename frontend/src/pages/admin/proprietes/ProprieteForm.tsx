/* cspell:disable */

import { useState } from 'react';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'sonner';
import apiClient from '@/shared/api/client';
import type { TypePropriete, Service } from '@/entities/propriete/types';

interface BailleurSimple { id: number; personne: { nom: string; prenom: string } }

const empty = {
  nom: '', ville: '', rue: '', quartier: '', nombre_piece: '', dimension: '',
  description: '', cout: '', typepropriete_id: '', bailleur_id: '', service_id: '',
};

export function ProprieteForm({ onClose }: Readonly<{ onClose: () => void }>) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState(empty);

  const { data: types } = useQuery({
    queryKey: ['typeproprietes'],
    queryFn: () => apiClient.get<TypePropriete[]>('/typeproprietes').then((r) => r.data),
  });
  const { data: services } = useQuery({
    queryKey: ['services'],
    queryFn: () => apiClient.get<Service[]>('/services').then((r) => r.data),
  });
  const { data: bailleurs } = useQuery({
    queryKey: ['admin-bailleurs-simple'],
    queryFn: () => apiClient.get<BailleurSimple[]>('/admin/bailleurs').then((r) => r.data),
  });

  const setField = (key: keyof typeof form) =>
    (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) =>
      setForm((f) => ({ ...f, [key]: e.target.value }));

  const { mutate, isPending } = useMutation({
    mutationFn: () => apiClient.post('/admin/proprietes', {
      ...form,
      nombre_piece: Number(form.nombre_piece),
      dimension: Number(form.dimension),
      cout: Number(form.cout),
      typepropriete_id: Number(form.typepropriete_id),
      bailleur_id: Number(form.bailleur_id),
      service_id: Number(form.service_id),
    }),
    onSuccess: () => {
      toast.success('Propriété créée.');
      queryClient.invalidateQueries({ queryKey: ['admin-proprietes'] });
      queryClient.invalidateQueries({ queryKey: ['proprietes'] });
      onClose();
    },
    onError: (err: any) => {
      const errors = err?.response?.data?.errors;
      if (errors) Object.values(errors).flat().forEach((m) => toast.error(m as string));
      else toast.error('Erreur lors de la création.');
    },
  });

  return (
    <Card>
      <CardContent className="p-5">
        <form onSubmit={(e) => { e.preventDefault(); mutate(); }} className="space-y-3">
          <div className="grid grid-cols-2 gap-3">
            <div className="col-span-2"><Label>Nom</Label><Input required value={form.nom} onChange={setField('nom')} /></div>
            <div><Label>Ville</Label><Input required value={form.ville} onChange={setField('ville')} /></div>
            <div><Label>Quartier</Label><Input value={form.quartier} onChange={setField('quartier')} /></div>
            <div><Label>Rue</Label><Input value={form.rue} onChange={setField('rue')} /></div>
            <div><Label>Pièces</Label><Input type="number" required value={form.nombre_piece} onChange={setField('nombre_piece')} /></div>
            <div><Label>Surface (m²)</Label><Input type="number" required value={form.dimension} onChange={setField('dimension')} /></div>
            <div><Label>Coût (FCFA)</Label><Input type="number" required value={form.cout} onChange={setField('cout')} /></div>
            <div>
              <Label>Type</Label>
              <select className="border rounded-md px-2 py-1.5 text-sm w-full" required value={form.typepropriete_id} onChange={setField('typepropriete_id')}>
                <option value="">Choisir...</option>
                {types?.map((t) => <option key={t.id} value={t.id}>{t.nom}</option>)}
              </select>
            </div>
            <div>
              <Label>Service</Label>
              <select className="border rounded-md px-2 py-1.5 text-sm w-full" required value={form.service_id} onChange={setField('service_id')}>
                <option value="">Choisir...</option>
                {services?.map((s) => <option key={s.id} value={s.id}>{s.nom}</option>)}
              </select>
            </div>
            <div>
              <Label>Bailleur</Label>
              <select className="border rounded-md px-2 py-1.5 text-sm w-full" required value={form.bailleur_id} onChange={setField('bailleur_id')}>
                <option value="">Choisir...</option>
                {bailleurs?.map((b) => <option key={b.id} value={b.id}>{b.personne.prenom} {b.personne.nom}</option>)}
              </select>
            </div>
            <div className="col-span-2">
              <Label>Description</Label>
              <textarea
                className="border rounded-md px-3 py-2 text-sm w-full min-h-20 resize-none"
                required
                value={form.description}
                onChange={setField('description')}
              />
            </div>
          </div>
          <div className="flex gap-2">
            <Button type="submit" disabled={isPending}>{isPending ? 'Création...' : 'Créer la propriété'}</Button>
            <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}