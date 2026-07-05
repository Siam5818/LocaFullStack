/* cspell:disable */

import { useState } from 'react';
import { useMutation } from '@tanstack/react-query';
import { Header } from '@/widgets/header/Header';
import { Footer } from '@/widgets/footer/Footer';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useAuth } from '@/features/auth/useAuth';
import { toast } from 'sonner';
import apiClient from '@/shared/api/client';

export default function ProfilPage() {
  const { user } = useAuth();
  const [form, setForm] = useState({
    nom: user?.nom ?? '',
    prenom: user?.prenom ?? '',
    telephone: user?.telephone ?? '',
  });
  const [pwForm, setPwForm] = useState({
    current_password: '',
    password: '',
    password_confirmation: '',
  });

  const set = (key: keyof typeof form) =>
    (e: React.ChangeEvent<HTMLInputElement>) =>
      setForm((f) => ({ ...f, [key]: e.target.value }));

  const setPw = (key: keyof typeof pwForm) =>
    (e: React.ChangeEvent<HTMLInputElement>) =>
      setPwForm((f) => ({ ...f, [key]: e.target.value }));

  const { mutate: updateProfil, isPending: profilPending } = useMutation({
    mutationFn: () => apiClient.put('/me', form),
    onSuccess: () => toast.success('Profil mis à jour.'),
    onError: (err: any) => toast.error(err?.response?.data?.message ?? 'Erreur.'),
  });

  const { mutate: updatePassword, isPending: pwPending } = useMutation({
    mutationFn: () => apiClient.put('/me/password', pwForm),
    onSuccess: () => {
      toast.success('Mot de passe mis à jour.');
      setPwForm({ current_password: '', password: '', password_confirmation: '' });
    },
    onError: (err: any) => toast.error(err?.response?.data?.message ?? 'Mot de passe actuel incorrect.'),
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1 max-w-2xl mx-auto px-4 py-8 w-full space-y-6">
        <h1 className="text-2xl font-bold">Mon profil</h1>

        <Card>
          <CardContent className="p-5 space-y-4">
            <h2 className="font-semibold">Informations personnelles</h2>
            <div className="grid grid-cols-2 gap-3">
              <div><Label>Nom</Label><Input value={form.nom} onChange={set('nom')} /></div>
              <div><Label>Prénom</Label><Input value={form.prenom} onChange={set('prenom')} /></div>
              <div className="col-span-2"><Label>Téléphone</Label><Input value={form.telephone} onChange={set('telephone')} /></div>
              <div className="col-span-2">
                <Label>E-mail</Label>
                <Input value={user?.email ?? ''} disabled className="bg-gray-50 text-gray-400" />
                <p className="text-xs text-gray-400 mt-1">L'adresse e-mail ne peut pas être modifiée ici.</p>
              </div>
            </div>
            <Button onClick={() => updateProfil()} disabled={profilPending}>
              {profilPending ? 'Mise à jour...' : 'Sauvegarder'}
            </Button>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-5 space-y-4">
            <h2 className="font-semibold">Changer mon mot de passe</h2>
            <div className="space-y-3">
              <div><Label>Mot de passe actuel</Label><Input type="password" value={pwForm.current_password} onChange={setPw('current_password')} /></div>
              <div>
                <Label>Nouveau mot de passe</Label>
                <Input type="password" value={pwForm.password} onChange={setPw('password')} />
                <p className="text-xs text-gray-400 mt-1">8 caractères min., une majuscule, un chiffre.</p>
              </div>
              <div><Label>Confirmer le nouveau mot de passe</Label><Input type="password" value={pwForm.password_confirmation} onChange={setPw('password_confirmation')} /></div>
            </div>
            <Button onClick={() => updatePassword()} disabled={pwPending}>
              {pwPending ? 'Mise à jour...' : 'Changer le mot de passe'}
            </Button>
          </CardContent>
        </Card>
      </main>
      <Footer />
    </div>
  );
}