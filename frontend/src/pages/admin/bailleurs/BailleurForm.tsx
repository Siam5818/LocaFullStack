/* cspell:disable */

import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'sonner';
import apiClient from '@/shared/api/client';

const empty = { nom: '', prenom: '', email: '', telephone: '', password: '', iban: '', nom_banque: '' };

export function BailleurForm({ onClose }: Readonly<{ onClose: () => void }>) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState(empty);

  const set = (key: keyof typeof form) =>
    (e: React.ChangeEvent<HTMLInputElement>) =>
      setForm((f) => ({ ...f, [key]: e.target.value }));

  const { mutate, isPending } = useMutation({
    mutationFn: () => apiClient.post('/admin/bailleurs', form),
    onSuccess: () => {
      toast.success('Bailleur créé.');
      queryClient.invalidateQueries({ queryKey: ['admin-bailleurs'] });
      onClose();
    },
    onError: (err: any) => {
      const errors = err?.response?.data?.errors;
      if (errors) Object.values(errors).flat().forEach((m) => toast.error(m as string));
      else toast.error(err?.response?.data?.message ?? 'Erreur.');
    },
  });

  return (
    <Card>
      <CardContent className="p-5">
        <form onSubmit={(e) => { e.preventDefault(); mutate(); }} className="space-y-3">
          <div className="grid grid-cols-2 gap-3">
            <div><Label>Nom</Label><Input required value={form.nom} onChange={set('nom')} /></div>
            <div><Label>Prénom</Label><Input required value={form.prenom} onChange={set('prenom')} /></div>
            <div><Label>E-mail</Label><Input type="email" required value={form.email} onChange={set('email')} /></div>
            <div><Label>Téléphone</Label><Input required value={form.telephone} onChange={set('telephone')} /></div>
            <div><Label>Mot de passe initial</Label><Input type="password" required value={form.password} onChange={set('password')} /></div>
            <div><Label>IBAN (optionnel)</Label><Input value={form.iban} onChange={set('iban')} /></div>
            <div className="col-span-2"><Label>Banque (optionnel)</Label><Input value={form.nom_banque} onChange={set('nom_banque')} /></div>
          </div>
          <div className="flex gap-2">
            <Button type="submit" disabled={isPending}>{isPending ? 'Création...' : 'Créer le bailleur'}</Button>
            <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}